<?php

namespace App\Services;

use App\Models\Level;
use App\Models\LessonProgress;
use App\Models\User;

class LessonProgression
{
    /**
     * Build the full progression tree for a student (D72).
     * Returns an array of levels, each:
     *   ['level' => Level, 'state' => 'open'|'locked'|'complete',
     *    'lessons' => [ ['lesson' => Lesson, 'state' => 'completed'|'unlocked'|'locked'], ... ]]
     *
     * Rules: D69 (level-gated, linear within level), D70 (published lessons only),
     * D71 (derived live).
     */
    public function forStudent(User $student): array
    {
        // Levels in order, each with only PUBLISHED lessons in order (D70).
        $levels = Level::with(['lessons' => function ($q) {
            $q->where('status', 'published')->orderBy('position');
        }])->orderBy('position')->get();

        // Which lesson ids this student has completed.
        $completedIds = LessonProgress::where('user_id', $student->id)
            ->where('completed', true)
            ->pluck('lesson_id')
            ->all();
        $completed = array_flip($completedIds);

        $tree = [];
        $previousLevelComplete = true; // Level 1 is always open (D69).

        foreach ($levels as $level) {
            $levelOpen = $previousLevelComplete;
            $lessons = $level->lessons;

            $lessonRows = [];
            $allPassed = true;
            // Within an open level, lessons unlock linearly (D69):
            // a lesson is unlocked if it's the first, or the previous lesson is completed.
            $previousLessonComplete = true;

            foreach ($lessons as $lesson) {
                $isCompleted = isset($completed[$lesson->id]);

                if (! $levelOpen) {
                    $state = 'locked';
                } elseif ($isCompleted) {
                    $state = 'completed';
                } elseif ($previousLessonComplete) {
                    $state = 'unlocked';
                } else {
                    $state = 'locked';
                }

                $lessonRows[] = ['lesson' => $lesson, 'state' => $state];

                if (! $isCompleted) {
                    $allPassed = false;
                }
                $previousLessonComplete = $isCompleted;
            }

            // A level with no published lessons counts as complete (nothing to block on).
            $levelComplete = $levelOpen && $allPassed;

            $tree[] = [
                'level' => $level,
                'state' => ! $levelOpen ? 'locked' : ($levelComplete ? 'complete' : 'open'),
                'lessons' => $lessonRows,
            ];

            $previousLevelComplete = $levelComplete;
        }

        return $tree;
    }

    /**
     * Is one specific lesson unlocked for the student? (Used by S2 to gate opening.)
     * Looks the lesson up in the full tree — single source of truth.
     */
    public function isLessonAccessible(User $student, int $lessonId): bool
    {
        foreach ($this->forStudent($student) as $levelRow) {
            foreach ($levelRow['lessons'] as $row) {
                if ($row['lesson']->id === $lessonId) {
                    return in_array($row['state'], ['unlocked', 'completed'], true);
                }
            }
        }
        return false; // lesson not found / not published / locked
    }
}
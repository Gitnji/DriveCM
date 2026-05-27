<?php

namespace App\Services;

use App\Models\Level;
use App\Models\LessonProgress;
use App\Models\User;

class LessonProgression
{
    public function forStudent(User $student): array
    {
        $levels = Level::with(['lessons' => function ($q) {
            $q->where('status', 'published')->orderBy('position');
        }])->orderBy('position')->get();

        $completedIds = LessonProgress::where('user_id', $student->id)
            ->where('completed', true)
            ->pluck('lesson_id')
            ->all();
        $completed = array_flip($completedIds);

        $tree = [];
        $previousLevelComplete = true;

        foreach ($levels as $level) {
            $levelOpen = $previousLevelComplete;
            $lessons = $level->lessons;

            $lessonRows = [];
            $allPassed = true;
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

    public function isLessonAccessible(User $student, int $lessonId): bool
    {
        foreach ($this->forStudent($student) as $levelRow) {
            foreach ($levelRow['lessons'] as $row) {
                if ($row['lesson']->id === $lessonId) {
                    return in_array($row['state'], ['unlocked', 'completed'], true);
                }
            }
        }
        return false;
    }

    /**
     * Has the student completed the first N levels (by position)? (D85 — practical theory gate.)
     * An empty level counts as complete (D70) — its tree state is 'complete'.
     */
    public function hasCompletedFirstLevels(User $student, int $count): bool
    {
        $tree = $this->forStudent($student);
        $firstN = array_slice($tree, 0, $count);

        // Fewer than N levels exist -> treat as not gated-out (don't freeze on missing levels).
        if (count($firstN) < $count) {
            return true;
        }

        foreach ($firstN as $levelRow) {
            if ($levelRow['state'] !== 'complete') {
                return false;
            }
        }
        return true;
    }
}
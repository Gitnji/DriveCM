<?php

namespace App\Actions;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;

class CompleteLesson
{
    /**
     * Mark a test-less lesson (D74) complete for a student.
     * Sets lesson_progress.completed only — creates NO lesson_attempts row (D79).
     * Idempotent: re-calling on an already-complete lesson does nothing.
     */
    public function execute(User $student, Lesson $lesson): void
    {
        // Guard: this path is ONLY for lessons with no questions (D79).
        if ($lesson->questions()->exists()) {
            throw new \LogicException('CompleteLesson called on a lesson that has questions.');
        }

        $progress = LessonProgress::firstOrNew([
            'user_id' => $student->id,
            'lesson_id' => $lesson->id,
        ]);

        if ($progress->completed) {
            return; // already complete — idempotent
        }

        $progress->completed = true;
        $progress->completed_at = now();
        // best_score / attempt_count stay at their defaults (0) — there was no test.
        $progress->save();
    }
}
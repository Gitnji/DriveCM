<?php

namespace App\Services;

use App\Models\Lesson;
use App\Models\LessonAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GradeAttempt
{
    /**
     * Grade a submitted test for a lesson.
     *
     * @param  array  $answers  [question_id => chosen_option_id]
     * @return array  ['score'=>int, 'passed'=>bool, 'attempt'=>LessonAttempt]
     */
    public function execute(User $student, Lesson $lesson, array $answers): array
    {
        // Load this lesson's questions with their options (tenant-scoped via trait).
        $questions = $lesson->questions()->with('options')->get();

        $total = $questions->count();

        if ($total === 0) {
            // A test-less lesson must not be graded — that is CompleteLesson's job (D79).
            throw new \LogicException('GradeAttempt called on a lesson with no questions.');
        }

        // --- Server-side grading (D76) ---
        $correct = 0;
        foreach ($questions as $question) {
            $chosenOptionId = $answers[$question->id] ?? null;
            $correctOption = $question->options->firstWhere('is_correct', true);

            if ($correctOption && (int) $chosenOptionId === $correctOption->id) {
                $correct++;
            }
        }

        $score = (int) round(($correct / $total) * 100);
        // D41 — passed is frozen against the threshold AT attempt time.
        $passed = $score >= $lesson->pass_threshold;

        return DB::transaction(function () use ($student, $lesson, $answers, $score, $passed) {
            // Record the attempt (D78 — full history, append-only).
            $attempt = LessonAttempt::create([
                'lesson_id' => $lesson->id,
                'user_id' => $student->id,
                'score' => $score,
                'passed' => $passed,
                'answers' => $answers,
            ]);

            // Update the derived progress summary (D35).
            $progress = LessonProgress::firstOrNew([
                'user_id' => $student->id,
                'lesson_id' => $lesson->id,
            ]);

            $progress->attempt_count = ($progress->attempt_count ?? 0) + 1;
            $progress->best_score = max($progress->best_score ?? 0, $score);

            // completed latches true once passed — never reverts on a later worse attempt.
            if ($passed && ! $progress->completed) {
                $progress->completed = true;
                $progress->completed_at = now();
            }

            // tenant_id auto-filled by the BelongsToTenant trait on a new row;
            // for an existing row it is already set.
            $progress->save();

            return ['score' => $score, 'passed' => $passed, 'attempt' => $attempt];
        });
    }
}
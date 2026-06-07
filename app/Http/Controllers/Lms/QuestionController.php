<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreQuestionRequest;
use App\Models\Lesson;
use App\Models\Question;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index(Lesson $lesson)
    {
        // Tenant scope on Lesson via the trait — a foreign lesson 404s.
        $questions = $lesson->questions()->with('options')->orderBy('position')->get();

        return view('lms.questions.index', [
            'lesson' => $lesson,
            'questions' => $questions,
        ]);
    }

    public function store(StoreQuestionRequest $request, Lesson $lesson)
    {
        $payload = $request->validated()['question'];
        $this->saveQuestion($lesson, $payload, null);

        return redirect()
            ->route('lms.questions.index', $lesson)
            ->with('status', __('Question added.'));
    }

    public function update(StoreQuestionRequest $request, Lesson $lesson, Question $question)
    {
        // Ownership: question must belong to this lesson (and tenant scope covers the rest).
        abort_unless($question->lesson_id === $lesson->id, 404);

        $payload = $request->validated()['question'];
        $this->saveQuestion($lesson, $payload, $question);

        return redirect()
            ->route('lms.questions.index', $lesson)
            ->with('status', __('Question updated.'));
    }

    public function destroy(Lesson $lesson, Question $question)
    {
        abort_unless($question->lesson_id === $lesson->id, 404);
        $question->delete(); // options cascade via FK

        return redirect()
            ->route('lms.questions.index', $lesson)
            ->with('status', __('Question deleted.'));
    }

    /**
     * Atomic save (D66): write the question + its options together, all or nothing.
     * For an update, options are replaced wholesale (simplest correct approach for a
     * fully-validated payload).
     */
  private function saveQuestion(Lesson $lesson, array $payload, ?Question $question): void
    {
        DB::transaction(function () use ($lesson, $payload, $question) {
            // P3a — image_upload_id is optional. Cast nullable int if present.
            $imageId = isset($payload['image_upload_id']) && $payload['image_upload_id'] !== null
                ? (int) $payload['image_upload_id']
                : null;

            if ($question === null) {
                $position = ($lesson->questions()->max('position') ?? 0) + 1;
                $question = $lesson->questions()->create([
                    'prompt'          => $payload['prompt'],
                    'type'            => $payload['type'],
                    'position'        => $position,
                    'image_upload_id' => $imageId,
                ]);
            } else {
                $question->update([
                    'prompt'          => $payload['prompt'],
                    'type'            => $payload['type'],
                    'image_upload_id' => $imageId,
                ]);
                $question->options()->delete(); // replace options wholesale
            }

            foreach (array_values($payload['options']) as $i => $opt) {
                $question->options()->create([
                    'text'       => $opt['text'],
                    'is_correct' => $opt['is_correct'],
                    'position'   => $i + 1,
                ]);
            }
        });
    }

    /**
     * L5 — reorder a question within its lesson by swapping position values with the
     * adjacent question. Atomic.
     */
    public function reorder(Lesson $lesson, Question $question, string $direction)
    {
        abort_unless($question->lesson_id === $lesson->id, 404);
        abort_unless(in_array($direction, ['up', 'down'], true), 404);

        $adjacent = Question::where('lesson_id', $lesson->id)
            ->when($direction === 'up',
                fn ($q) => $q->where('position', '<', $question->position)->orderByDesc('position'),
                fn ($q) => $q->where('position', '>', $question->position)->orderBy('position')
            )
            ->first();

        if (! $adjacent) {
            return redirect()->route('lms.questions.index', $lesson);
        }

        DB::transaction(function () use ($question, $adjacent) {
            $a = $question->position;
            $b = $adjacent->position;
            $question->update(['position' => $b]);
            $adjacent->update(['position' => $a]);
        });

        return redirect()
            ->route('lms.questions.index', $lesson)
            ->with('status', __('Question order updated.'));
    }
}
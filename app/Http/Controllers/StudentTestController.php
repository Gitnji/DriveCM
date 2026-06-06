<?php

namespace App\Http\Controllers;

use App\Actions\CompleteLesson;
use App\Http\Requests\StudentTestRequest;
use App\Models\Lesson;
use App\Models\LessonAttempt;
use App\Models\LessonProgress;
use App\Services\GradeAttempt;
use App\Services\LessonProgression;
use Illuminate\Support\Facades\Auth;

class StudentTestController extends Controller
{
    public function show(Lesson $lesson, LessonProgression $progression)
    {
        $student = Auth::guard('web')->user();
        abort_unless($progression->isLessonAccessible($student, $lesson->id), 403);

        // P3a — eager-load image alongside options to avoid N+1.
        $questions = $lesson->questions()->with(['options', 'image'])->orderBy('position')->get();

        if ($questions->isEmpty()) {
            return redirect()->route('student.lessons.show', $lesson);
        }

        return view('student.test.show', [
            'lesson' => $lesson,
            'questions' => $questions,
        ]);
    }

    public function submit(StudentTestRequest $request, Lesson $lesson, GradeAttempt $grader, LessonProgression $progression)
    {
        $student = Auth::guard('web')->user();
        abort_unless($progression->isLessonAccessible($student, $lesson->id), 403);
        abort_if($lesson->questions()->doesntExist(), 404);

        $answers = collect($request->validated()['answers'])
            ->mapWithKeys(fn ($optId, $qId) => [(int) $qId => (int) $optId])
            ->all();

        $result = $grader->execute($student, $lesson, $answers);

        return redirect()->route('student.test.result', [$lesson, $result['attempt']->id]);
    }

    public function finish(Lesson $lesson, CompleteLesson $completer, LessonProgression $progression)
    {
        $student = Auth::guard('web')->user();
        abort_unless($progression->isLessonAccessible($student, $lesson->id), 403);
        abort_unless($lesson->questions()->doesntExist(), 404);

        $completer->execute($student, $lesson);

        return redirect()->route('student.lessons.index')
            ->with('status', __('Lesson completed.'));
    }

    public function result(Lesson $lesson, LessonAttempt $attempt)
    {
        $student = Auth::guard('web')->user();
        abort_unless(
            $attempt->user_id === $student->id && $attempt->lesson_id === $lesson->id,
            403
        );

        // P3a — eager-load image so the result page can show images alongside questions.
        $questions = $lesson->questions()->with(['options', 'image'])->orderBy('position')->get();

        $hasPassed = LessonProgress::where('user_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->value('completed') ?? false;

        return view('student.test.result', [
            'lesson' => $lesson,
            'attempt' => $attempt,
            'questions' => $questions,
            'revealAnswers' => (bool) $hasPassed,
        ]);
    }
}
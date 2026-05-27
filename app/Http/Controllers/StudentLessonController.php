<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\PracticalSession;
use App\Services\LessonProgression;
use Illuminate\Support\Facades\Auth;

class StudentLessonController extends Controller
{
    public function index(LessonProgression $progression)
    {
        $student = Auth::guard('web')->user();
        $tree = $progression->forStudent($student);

        return view('student.lessons.index', ['tree' => $tree]);
    }

    public function show(Lesson $lesson, LessonProgression $progression)
    {
        $student = Auth::guard('web')->user();

        abort_unless(
            $progression->isLessonAccessible($student, $lesson->id),
            403
        );

        return view('student.lessons.show', ['lesson' => $lesson]);
    }

    public function practical()
    {
        $student = Auth::guard('web')->user();

        $sessions = PracticalSession::with('instructor')
            ->where('student_id', $student->id)
            ->orderByDesc('scheduled_at')
            ->get();

        // D89 — practical hours: sum of duration over completed sessions.
        $practicalMinutes = $sessions->where('status', 'completed')->sum('duration_minutes');

        // D40 — theory hours: sum of duration over completed lessons.
        $completedLessonIds = LessonProgress::where('user_id', $student->id)
            ->where('completed', true)
            ->pluck('lesson_id');
        $theoryMinutes = Lesson::whereIn('id', $completedLessonIds)->sum('duration_minutes');

        return view('student.practical.index', [
            'sessions' => $sessions,
            'practicalMinutes' => $practicalMinutes,
            'theoryMinutes' => $theoryMinutes,
        ]);
    }
}
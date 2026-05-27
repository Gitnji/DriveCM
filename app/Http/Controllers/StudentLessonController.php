<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
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

        // Server-side gate: a locked (or draft, or foreign) lesson must not open.
        // isLessonAccessible only returns true for unlocked/completed published lessons.
        abort_unless(
            $progression->isLessonAccessible($student, $lesson->id),
            403
        );

        return view('student.lessons.show', ['lesson' => $lesson]);
    }
}
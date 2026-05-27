<?php

namespace App\Http\Controllers\Lms;

use App\Actions\SanitizeLessonBlocks;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreLessonRequest;
use App\Http\Requests\Lms\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Level;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::with('level')
            ->orderBy('level_id')
            ->orderBy('position')
            ->get();

        return view('lms.lessons.index', ['lessons' => $lessons]);
    }

    public function create()
    {
        return view('lms.lessons.form', [
            'lesson' => new Lesson(),
            'levels' => Level::orderBy('position')->get(),
        ]);
    }

    public function store(StoreLessonRequest $request, SanitizeLessonBlocks $sanitizer)
    {
        $data = $request->validated();
        $data['content'] = $sanitizer->execute($data['content'] ?? []);

        Lesson::create($data);

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson created.'));
    }

    public function edit(Lesson $lesson)
    {
        return view('lms.lessons.form', [
            'lesson' => $lesson,
            'levels' => Level::orderBy('position')->get(),
        ]);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson, SanitizeLessonBlocks $sanitizer)
    {
        $data = $request->validated();
        $data['content'] = $sanitizer->execute($data['content'] ?? []);

        $lesson->update($data);

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson updated.'));
    }

    public function destroy(Lesson $lesson)
    {
        $lesson->delete();

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson deleted.'));
    }
}
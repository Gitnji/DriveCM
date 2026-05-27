<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreLessonRequest;
use App\Http\Requests\Lms\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Level;

class LessonController extends Controller
{
    public function index()
    {
        // Trait scopes to session tenant. Eager-load level for display.
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

    public function store(StoreLessonRequest $request)
    {
        // tenant_id auto-filled by the BelongsToTenant trait.
        Lesson::create($request->validated());

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson created.'));
    }

    public function edit(Lesson $lesson)
    {
        // Route-model binding + tenant global scope = a foreign lesson 404s.
        return view('lms.lessons.form', [
            'lesson' => $lesson,
            'levels' => Level::orderBy('position')->get(),
        ]);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson)
    {
        $lesson->update($request->validated());

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson updated.'));
    }

    public function destroy(Lesson $lesson)
    {
        // questions cascade-delete via the FK (set in the questions migration).
        $lesson->delete();

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson deleted.'));
    }
}
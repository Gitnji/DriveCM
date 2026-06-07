<?php

namespace App\Http\Controllers\Lms;

use App\Actions\SanitizeLessonBlocks;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lms\StoreLessonRequest;
use App\Http\Requests\Lms\UpdateLessonRequest;
use App\Models\Lesson;
use App\Models\Level;
use Illuminate\Support\Facades\DB;

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

    /**
     * L4 — reorder a lesson within its level by swapping position values with the
     * adjacent lesson (previous or next, depending on direction). Atomic.
     */
    public function reorder(Lesson $lesson, string $direction)
    {
        abort_unless(in_array($direction, ['up', 'down'], true), 404);

        // Find the adjacent lesson in the same level.
        $adjacent = Lesson::where('level_id', $lesson->level_id)
            ->when($direction === 'up',
                fn ($q) => $q->where('position', '<', $lesson->position)->orderByDesc('position'),
                fn ($q) => $q->where('position', '>', $lesson->position)->orderBy('position')
            )
            ->first();

        // No-op if already at the edge.
        if (! $adjacent) {
            return redirect()->route('lms.lessons.index');
        }

        DB::transaction(function () use ($lesson, $adjacent) {
            $a = $lesson->position;
            $b = $adjacent->position;
            $lesson->update(['position' => $b]);
            $adjacent->update(['position' => $a]);
        });

        return redirect()
            ->route('lms.lessons.index')
            ->with('status', __('Lesson order updated.'));
    }
}
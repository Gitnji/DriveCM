@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-neutral">Lessons</h1>
            <a href="{{ route('lms.lessons.create') }}"
               class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                New lesson
            </a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <div class="mt-6 space-y-2">
            @forelse ($lessons as $i => $lesson)
                @php
                    // L4 — determine if this is first/last in its level for arrow disable states.
                    $prevLesson = $lessons[$i - 1] ?? null;
                    $nextLesson = $lessons[$i + 1] ?? null;
                    $isFirstInLevel = ! $prevLesson || $prevLesson->level_id !== $lesson->level_id;
                    $isLastInLevel  = ! $nextLesson || $nextLesson->level_id !== $lesson->level_id;
                @endphp
                <div class="flex items-center justify-between rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-center gap-3">
                        {{-- L4 — reorder arrows --}}
                        <div class="flex flex-col gap-0.5">
                            <form method="POST" action="{{ route('lms.lessons.reorder', [$lesson, 'up']) }}">
                                @csrf
                                <button type="submit"
                                        @disabled($isFirstInLevel)
                                        title="Move up"
                                        class="rounded p-1 text-neutral/40 hover:text-primary disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:text-neutral/40">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                         stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                        <polyline points="18 15 12 9 6 15"/>
                                    </svg>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('lms.lessons.reorder', [$lesson, 'down']) }}">
                                @csrf
                                <button type="submit"
                                        @disabled($isLastInLevel)
                                        title="Move down"
                                        class="rounded p-1 text-neutral/40 hover:text-primary disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:text-neutral/40">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                         stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                        <polyline points="6 9 12 15 18 9"/>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-neutral">{{ $lesson->title }}</div>
                            <div class="text-xs text-neutral/50">
                                {{ $lesson->level->name }} · position {{ $lesson->position }} ·
                                <span class="{{ $lesson->isPublished() ? 'text-success' : 'text-accent' }}">{{ ucfirst($lesson->status) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('lms.questions.index', $lesson) }}" class="text-sm font-medium text-primary hover:underline">Questions</a>
                        <a href="{{ route('lms.lessons.edit', $lesson) }}" class="text-sm font-medium text-primary hover:underline">Edit</a>
                        <form method="POST" action="{{ route('lms.lessons.destroy', $lesson) }}"
                              onsubmit="return confirm('Delete this lesson?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-8 text-center text-sm text-neutral/50">
                    No lessons yet. Create your first one.
                </p>
            @endforelse
        </div>
    </div>
@endsection
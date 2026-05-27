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
            @forelse ($lessons as $lesson)
                <div class="flex items-center justify-between rounded-xl border border-neutral/10 bg-white p-4">
                    <div>
                        <div class="text-sm font-medium text-neutral">{{ $lesson->title }}</div>
                        <div class="text-xs text-neutral/50">
                            {{ $lesson->level->name }} · position {{ $lesson->position }} ·
                            <span class="{{ $lesson->isPublished() ? 'text-success' : 'text-accent' }}">{{ ucfirst($lesson->status) }}</span>
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
@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('student.lessons.show', $lesson) }}" class="text-sm font-medium text-primary hover:underline">← Back to lesson</a>
        <h1 class="mt-3 text-xl font-semibold text-neutral">Test — {{ $lesson->title }}</h1>
        <p class="mt-1 text-sm text-neutral/60">Answer all questions. You need {{ $lesson->pass_threshold }}% to pass.</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('student.test.submit', $lesson) }}" class="mt-6 space-y-6">
            @csrf

            @foreach ($questions as $i => $question)
                <div class="rounded-xl border border-neutral/10 bg-white p-5">
                    <p class="text-sm font-medium text-neutral">
                        {{ $i + 1 }}. {{ $question->prompt }}
                    </p>
                    <div class="mt-3 space-y-2">
                        @foreach ($question->options as $option)
                            <label class="flex items-center gap-2 rounded-lg border border-neutral/15 px-3 py-2 text-sm hover:bg-surface">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required>
                                <span>{{ $option->text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <button type="submit"
                class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Submit test
            </button>
        </form>
    </div>
@endsection
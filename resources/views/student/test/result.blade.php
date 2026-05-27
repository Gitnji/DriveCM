@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-xl font-semibold text-neutral">Test result — {{ $lesson->title }}</h1>

        @php($passed = $attempt->passed)
        <div class="mt-4 rounded-xl border p-5 {{ $passed ? 'border-success/30 bg-success/5' : 'border-accent/30 bg-accent/5' }}">
            <div class="text-2xl font-bold {{ $passed ? 'text-success' : 'text-accent' }}">
                {{ $attempt->score }}%
            </div>
            <p class="mt-1 text-sm {{ $passed ? 'text-success' : 'text-neutral/60' }}">
                {{ $passed ? 'Passed — well done.' : 'Not passed yet. You need ' . $lesson->pass_threshold . '%.' }}
            </p>
        </div>

        @php($answers = $attempt->answers ?? [])

        <div class="mt-6 space-y-4">
            @foreach ($questions as $i => $question)
                @php($chosen = (int) ($answers[$question->id] ?? 0))
                @php($correctOption = $question->options->firstWhere('is_correct', true))
                @php($wasCorrect = $correctOption && $chosen === $correctOption->id)

                <div class="rounded-xl border border-neutral/10 bg-white p-5">
                    <div class="flex items-start justify-between">
                        <p class="text-sm font-medium text-neutral">{{ $i + 1 }}. {{ $question->prompt }}</p>
                        <span class="ml-3 shrink-0 text-xs font-semibold {{ $wasCorrect ? 'text-success' : 'text-red-600' }}">
                            {{ $wasCorrect ? '✓ Correct' : '✗ Wrong' }}
                        </span>
                    </div>

                    {{-- D77: full answers ONLY once the student has passed this lesson. --}}
                    @if ($revealAnswers)
                        <div class="mt-3 space-y-1">
                            @foreach ($question->options as $option)
                                @php($isChosen = $chosen === $option->id)
                                @php($isRight = $option->is_correct)
                                <div class="rounded-lg px-3 py-1.5 text-sm
                                    {{ $isRight ? 'bg-success/10 text-success font-medium' : '' }}
                                    {{ $isChosen && ! $isRight ? 'bg-red-50 text-red-700' : '' }}
                                    {{ ! $isChosen && ! $isRight ? 'text-neutral/60' : '' }}">
                                    {{ $option->text }}
                                    @if ($isRight) <span class="text-xs">— correct answer</span>
                                    @elseif ($isChosen) <span class="text-xs">— your answer</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="mt-2 text-xs text-neutral/50">
                            Pass this lesson to see the correct answers.
                        </p>
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex gap-3 border-t border-neutral/10 pt-6">
            @unless ($passed)
                <a href="{{ route('student.test.show', $lesson) }}"
                   class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    Retake test
                </a>
            @endunless
            <a href="{{ route('student.lessons.index') }}"
               class="rounded-lg border border-neutral/20 px-5 py-2.5 text-sm font-semibold text-neutral hover:bg-surface">
                Back to My Lessons
            </a>
        </div>
    </div>
@endsection
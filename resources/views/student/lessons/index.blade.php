@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-xl font-semibold text-neutral">My Lessons</h1>
        <p class="mt-1 text-sm text-neutral/60">Complete each lesson's test to unlock the next.</p>

        <div class="mt-6 space-y-6">
            @foreach ($tree as $levelRow)
                @php($level = $levelRow['level'])
                @php($isEmpty = count($levelRow['lessons']) === 0)
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <h2 class="text-sm font-semibold text-neutral">{{ $level->name }}</h2>
                        @if ($isEmpty)
                            {{-- D81 — an empty level is "Coming soon" to the student, not "Complete". --}}
                            <span class="rounded-full bg-neutral/10 px-2 py-0.5 text-xs font-medium text-neutral/50">Coming soon</span>
                        @elseif ($levelRow['state'] === 'complete')
                            <span class="rounded-full bg-success/10 px-2 py-0.5 text-xs font-medium text-success">Complete</span>
                        @elseif ($levelRow['state'] === 'locked')
                            <span class="rounded-full bg-neutral/10 px-2 py-0.5 text-xs font-medium text-neutral/50">Locked</span>
                        @endif
                    </div>

                    @if ($isEmpty)
                        <p class="rounded-lg border border-dashed border-neutral/20 p-4 text-center text-xs text-neutral/40">
                            Lessons for this level haven't been added yet.
                        </p>
                    @else
                        <div class="space-y-2">
                            @foreach ($levelRow['lessons'] as $row)
                                @php($lesson = $row['lesson'])
                                @if ($row['state'] === 'locked')
                                    <div class="flex items-center justify-between rounded-xl border border-neutral/10 bg-neutral/5 p-4">
                                        <span class="text-sm text-neutral/40">{{ $lesson->title }}</span>
                                        <span class="text-xs text-neutral/40">🔒 Locked</span>
                                    </div>
                                @else
                                    <a href="{{ route('student.lessons.show', $lesson) }}"
                                       class="flex items-center justify-between rounded-xl border border-neutral/10 bg-white p-4 hover:border-primary/40">
                                        <span class="text-sm font-medium text-neutral">{{ $lesson->title }}</span>
                                        @if ($row['state'] === 'completed')
                                            <span class="text-xs font-medium text-success">✓ Completed</span>
                                        @else
                                            <span class="text-xs font-medium text-primary">Start →</span>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-xl font-semibold text-neutral">My Lessons</h1>
        <p class="mt-1 text-sm text-neutral/60">Complete each lesson's test to unlock the next.</p>

        {{-- P3 — payment overdue banner. Shown when student has any unpaid required payment. --}}
        @if ($pendingPayments->isNotEmpty())
            <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-red-100 text-red-700">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-red-900">Payment required to continue</div>
                        <p class="mt-1 text-xs text-red-800/80">
                            You have {{ $pendingPayments->count() }} {{ \Illuminate\Support\Str::plural('outstanding payment', $pendingPayments->count()) }} to complete before you can access lessons:
                        </p>
                        <ul class="mt-2 space-y-1 text-xs text-red-900">
                            @foreach ($pendingPayments as $type)
                                <li class="flex items-center gap-2">
                                    <span class="font-medium">{{ $type->name }}</span>
                                    <span class="text-red-700/70">— {{ number_format($type->amount_xaf) }} XAF</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-3 text-xs text-red-800/70">
                            Visit your Payments page to submit proof of payment. (Page coming soon.)
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="mt-6 space-y-6">
            @foreach ($tree as $levelRow)
                @php($level = $levelRow['level'])
                @php($isEmpty = count($levelRow['lessons']) === 0)
                <div>
                    <div class="mb-2 flex items-center gap-2">
                        <h2 class="text-sm font-semibold text-neutral">{{ $level->name }}</h2>
                        @if ($isEmpty)
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
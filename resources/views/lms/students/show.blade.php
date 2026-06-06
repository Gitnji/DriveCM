@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <a href="{{ route('lms.students.index') }}" class="text-sm font-medium text-primary hover:underline">← Students</a>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Header --}}
    <div class="mt-4 flex items-start justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-xl font-semibold text-white">
                {{ \App\Support\InstructorAvatar::initials($student->name) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-neutral">{{ $student->name }}</h1>
                <div class="mt-1 text-sm text-neutral/50">
                    Enrolled {{ $student->created_at?->diffForHumans() ?? 'unknown' }}
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('lms.students.edit', $student) }}"
               class="rounded-lg border border-neutral/20 bg-white px-4 py-2 text-sm font-semibold text-neutral hover:bg-surface">Edit</a>
            <form method="POST" action="{{ route('lms.students.destroy', $student) }}"
                  onsubmit="return confirm('Remove {{ $student->name }}? They will lose access to the platform.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100">
                    Remove
                </button>
            </form>
        </div>
    </div>

    {{-- Section A — Contact info --}}
    <div class="mt-8 rounded-xl border border-neutral/10 bg-white p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Contact</h2>
        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Email</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $student->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Phone</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $student->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Town</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $student->town ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Language</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $student->language === 'fr' ? 'Français' : 'English' }}</dd>
            </div>
        </dl>
    </div>

    {{-- Section B — Theory progress --}}
    <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Theory progress</h2>
        <dl class="mt-4 grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-3">
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Current level</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $currentLevel?->name ?? 'Not started' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Lessons completed</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $lessonsCompleted }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Total attempts</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $lessonAttempts }}</dd>
            </div>
        </dl>
    </div>

    {{-- Section C — Practical --}}
    <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Practical sessions</h2>
        <dl class="mt-4 grid grid-cols-2 gap-x-6 gap-y-4 sm:grid-cols-4">
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Completed</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $sessionsCompleted }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">No-shows</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $sessionsNoShow }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">Total minutes</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $practicalMinutes }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold text-neutral/50">All sessions</dt>
                <dd class="mt-1 text-sm font-medium text-neutral">{{ $sessions->count() }}</dd>
            </div>
        </dl>

        @if ($sessions->isNotEmpty())
            <div class="mt-4 border-t border-neutral/10 pt-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Recent sessions</div>
                <div class="mt-3 space-y-2">
                    @foreach ($sessions->take(5) as $session)
                        @php
                            $statusBadge = match ($session->status) {
                                'scheduled' => ['bg-neutral/8',  'text-neutral/60', 'Scheduled'],
                                'completed' => ['bg-success/15', 'text-success',    'Completed'],
                                'no_show'   => ['bg-red-100',    'text-red-700',    'No-show'],
                                default     => ['bg-neutral/10', 'text-neutral/60', ucfirst($session->status)],
                            };
                        @endphp
                        <div class="flex items-center justify-between text-sm">
                            <div>
                                <span class="font-mono text-xs text-neutral/60">{{ $session->scheduled_at?->format('M j, H:i') ?? '—' }}</span>
                                <span class="text-neutral/30">·</span>
                                <span class="text-neutral">with {{ $session->instructor?->name ?? 'unassigned' }}</span>
                            </div>
                            <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                                {{ $statusBadge[2] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Section D — Ministry report --}}
    <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Ministry report</h2>
        <div class="mt-4">
            @if ($reportValidation)
                <div class="text-sm text-neutral">
                    <span class="font-semibold text-success">Validated</span>
                    {{ $reportValidation->created_at?->diffForHumans() ?? '' }}
                    by {{ $reportValidation->validatedBy?->name ?? 'unknown' }}
                </div>
                <div class="mt-1 text-xs text-neutral/50">
                    Theory minutes: {{ $reportValidation->theory_minutes }} · Practical minutes: {{ $reportValidation->practical_minutes }}
                </div>
            @else
                <div class="text-sm text-neutral/60">No report validated yet.</div>
            @endif
        </div>
    </div>
</div>
@endsection
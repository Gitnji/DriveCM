@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-8 lg:px-6">

    {{-- Heading --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral">
            Welcome, {{ $user->name }}
        </h1>
        <p class="mt-1 text-sm text-neutral/60">
            Signed in as {{ ucfirst($user->role) }}.
        </p>
    </div>

    @if ($user->role === 'owner' && $stats)
        {{-- DASH-2 (D163) — Owner dashboard --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Students</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['students_total'] }}</span>
                    <span class="text-sm text-neutral/50">total</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">
                    <span class="font-semibold text-success">{{ $stats['students_active'] }}</span> active in last 30 days
                </div>
            </div>

            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Lessons published</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['lessons_published'] }}</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">Theory content live for students.</div>
            </div>

            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Pages published</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['pages_published'] }}</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">On your public website.</div>
            </div>

            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Sessions this week</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['sessions_week'] }}</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">Practical lessons scheduled.</div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <span class="mr-2 text-xs font-semibold uppercase tracking-[0.15em] text-neutral/50">Quick actions</span>
            @can('author-lessons')
                <a href="{{ route('lms.lessons.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    New lesson
                </a>
            @endcan
            @can('manage-site')
                <a href="{{ route('site.pages.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
                    </svg>
                    Edit site
                </a>
            @endcan
            @can('preview-reports')
                <a href="{{ route('lms.reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    View reports
                </a>
            @endcan
        </div>

    @elseif ($user->role === 'secretary' && $stats)
        {{-- DASH-3 (D166) — Secretary dashboard --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Today's sessions — static --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Today's sessions</div>
                <div class="mt-2 text-3xl font-bold text-neutral">{{ $stats['sessions_today'] }}</div>
                <div class="mt-3 text-xs text-neutral/50">Scheduled for {{ today()->format('M j') }}.</div>
            </div>

            {{-- Needs attendance — static (but accent-tinted when > 0) --}}
            <div class="rounded-xl border {{ $stats['sessions_unmarked'] > 0 ? 'border-accent/30 bg-accent/5' : 'border-neutral/10 bg-white' }} p-5">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide {{ $stats['sessions_unmarked'] > 0 ? 'text-accent-dark' : 'text-neutral/50' }}">
                    @if ($stats['sessions_unmarked'] > 0)
                        <span class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-accent"></span>
                    @endif
                    Needs attendance
                </div>
                <div class="mt-2 text-3xl font-bold {{ $stats['sessions_unmarked'] > 0 ? 'text-accent-dark' : 'text-neutral' }}">
                    {{ $stats['sessions_unmarked'] }}
                </div>
                <div class="mt-3 text-xs text-neutral/50">
                    @if ($stats['sessions_unmarked'] > 0)
                        Past sessions awaiting attendance.
                    @else
                        All caught up.
                    @endif
                </div>
            </div>

            {{-- Pending applications — CLICKABLE (only card with real navigation target) --}}
            <a href="{{ route('lms.enrollments.index') }}"
               class="group block rounded-xl border {{ $stats['pending_applications'] > 0 ? 'border-accent/30 bg-accent/5' : 'border-neutral/10 bg-white' }} p-5 transition hover:border-primary/40 hover:shadow-sm">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide {{ $stats['pending_applications'] > 0 ? 'text-accent-dark' : 'text-neutral/50' }}">
                    @if ($stats['pending_applications'] > 0)
                        <span class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-accent"></span>
                    @endif
                    Pending applications
                </div>
                <div class="mt-2 text-3xl font-bold {{ $stats['pending_applications'] > 0 ? 'text-accent-dark' : 'text-neutral' }}">
                    {{ $stats['pending_applications'] }}
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-neutral/50">
                    <span>
                        @if ($stats['pending_applications'] > 0)
                            New students awaiting review.
                        @else
                            No applications waiting.
                        @endif
                    </span>
                    <span class="text-neutral/30 transition-transform group-hover:translate-x-0.5">→</span>
                </div>
            </a>

            {{-- New this month — static --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">New this month</div>
                <div class="mt-2 text-3xl font-bold text-neutral">{{ $stats['students_this_month'] }}</div>
                <div class="mt-3 text-xs text-neutral/50">Students enrolled in {{ now()->format('F') }}.</div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <span class="mr-2 text-xs font-semibold uppercase tracking-[0.15em] text-neutral/50">Quick actions</span>

            @can('schedule-practical')
                <a href="{{ route('lms.practical.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Schedule practical
                </a>
            @endcan

            @can('review-enrollments')
                <a href="{{ route('lms.enrollments.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    Review applications
                    @if ($stats['pending_applications'] > 0)
                        <span class="rounded-full bg-accent/15 px-2 py-0.5 text-xs font-bold text-accent-dark">
                            {{ $stats['pending_applications'] }}
                        </span>
                    @endif
                </a>
            @endcan

            {{-- β — public form, context-switch out of app. Opens in new tab to soften the jump. --}}
            <a href="{{ route('register.create') }}" target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                Enroll student
                <span class="text-neutral/40">↗</span>
            </a>
        </div>

        {{-- Today's schedule list --}}
        <div class="mt-8 rounded-xl border border-neutral/10 bg-white">
            <div class="flex items-center justify-between border-b border-neutral/10 px-6 py-4">
                <h2 class="text-lg font-semibold text-neutral">Today's schedule</h2>
                <span class="text-xs text-neutral/50">{{ today()->format('l, F j') }}</span>
            </div>
            @if (! $today || $today->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral/50">No sessions scheduled today.</div>
            @else
                <div class="divide-y divide-neutral/10">
                    @foreach ($today as $session)
                        @php
                            $statusBadge = match ($session->status) {
                                'scheduled' => ['bg-neutral/8',  'text-neutral/60', 'Scheduled'],
                                'completed' => ['bg-success/15', 'text-success',    'Completed'],
                                'no_show'   => ['bg-red-100',    'text-red-700',    'No-show'],
                                default     => ['bg-neutral/10', 'text-neutral/60', ucfirst($session->status)],
                            };
                        @endphp
                        <div class="flex items-center justify-between px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="text-sm font-mono font-semibold text-neutral w-16">
                                    {{ $session->scheduled_at?->format('H:i') ?? '—' }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-neutral">
                                        {{ $session->student?->name ?? 'Unknown student' }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-neutral/50">
                                        with {{ $session->instructor?->name ?? 'unassigned' }}
                                    </div>
                                </div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                                {{ $statusBadge[2] }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-neutral/10 px-6 py-3">
                    <a href="{{ route('lms.practical.index') }}" class="text-sm font-semibold text-primary hover:underline">
                        View all sessions →
                    </a>
                </div>
            @endif
        </div>

   @elseif ($user->role === 'instructor' && $stats)
        {{-- DASH-4 (D167) — Instructor dashboard --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- My sessions today --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">My sessions today</div>
                <div class="mt-2 text-3xl font-bold text-neutral">{{ $stats['my_sessions_today'] }}</div>
                <div class="mt-3 text-xs text-neutral/50">Scheduled for {{ today()->format('M j') }}.</div>
            </div>

            {{-- Needs attendance — CLICKABLE, filtered link --}}
            <a href="{{ route('lms.practical.index') }}?status=needs_attention"
               class="group block rounded-xl border {{ $stats['my_needs_attention'] > 0 ? 'border-accent/30 bg-accent/5' : 'border-neutral/10 bg-white' }} p-5 transition hover:border-primary/40 hover:shadow-sm">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide {{ $stats['my_needs_attention'] > 0 ? 'text-accent-dark' : 'text-neutral/50' }}">
                    @if ($stats['my_needs_attention'] > 0)
                        <span class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-accent"></span>
                    @endif
                    Needs attendance
                </div>
                <div class="mt-2 text-3xl font-bold {{ $stats['my_needs_attention'] > 0 ? 'text-accent-dark' : 'text-neutral' }}">
                    {{ $stats['my_needs_attention'] }}
                </div>
                <div class="mt-3 flex items-center justify-between text-xs text-neutral/50">
                    <span>
                        @if ($stats['my_needs_attention'] > 0)
                            Past sessions awaiting attendance.
                        @else
                            All caught up.
                        @endif
                    </span>
                    <span class="text-neutral/30 transition-transform group-hover:translate-x-0.5">→</span>
                </div>
            </a>

            {{-- My sessions this week --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">My week</div>
                <div class="mt-2 text-3xl font-bold text-neutral">{{ $stats['my_sessions_week'] }}</div>
                <div class="mt-3 text-xs text-neutral/50">Sessions scheduled this week.</div>
            </div>

            {{-- My students --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">My students</div>
                <div class="mt-2 text-3xl font-bold text-neutral">{{ $stats['my_students'] }}</div>
                <div class="mt-3 text-xs text-neutral/50">Unique students you've taught.</div>
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <span class="mr-2 text-xs font-semibold uppercase tracking-[0.15em] text-neutral/50">Quick actions</span>

            @can('author-lessons')
                <a href="{{ route('lms.lessons.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    New lesson
                </a>
            @endcan

            @can('schedule-practical')
                <a href="{{ route('lms.practical.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    View my schedule
                </a>
            @endcan
        </div>

        {{-- Today's schedule list (instructor's own) --}}
        <div class="mt-8 rounded-xl border border-neutral/10 bg-white">
            <div class="flex items-center justify-between border-b border-neutral/10 px-6 py-4">
                <h2 class="text-lg font-semibold text-neutral">Today's schedule</h2>
                <span class="text-xs text-neutral/50">{{ today()->format('l, F j') }}</span>
            </div>
            @if (! $today || $today->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral/50">No sessions scheduled today.</div>
            @else
                <div class="divide-y divide-neutral/10">
                    @foreach ($today as $session)
                        @php
                            $statusBadge = match ($session->status) {
                                'scheduled' => ['bg-neutral/8',  'text-neutral/60', 'Scheduled'],
                                'completed' => ['bg-success/15', 'text-success',    'Completed'],
                                'no_show'   => ['bg-red-100',    'text-red-700',    'No-show'],
                                default     => ['bg-neutral/10', 'text-neutral/60', ucfirst($session->status)],
                            };
                        @endphp
                        <div class="flex items-center justify-between px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="text-sm font-mono font-semibold text-neutral w-16">
                                    {{ $session->scheduled_at?->format('H:i') ?? '—' }}
                                </div>
                                <div class="text-sm font-semibold text-neutral">
                                    {{ $session->student?->name ?? 'Unknown student' }}
                                </div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                                {{ $statusBadge[2] }}
                            </span>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-neutral/10 px-6 py-3">
                    <a href="{{ route('lms.practical.index') }}" class="text-sm font-semibold text-primary hover:underline">
                        View all sessions →
                    </a>
                </div>
            @endif
        </div>

    @elseif ($user->role === 'student' && $stats)
        {{-- DASH-5 (D167) — Student progress overview --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Current level --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Current level</div>
                <div class="mt-2 text-2xl font-bold text-neutral">
                    {{ $stats['current_level']?->name ?? 'Not started' }}
                </div>
                <div class="mt-3 text-xs text-neutral/50">
                    @if ($stats['current_level'])
                        Currently working on this level.
                    @else
                        Begin your first lesson to start.
                    @endif
                </div>
            </div>

            {{-- Lessons completed --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Lessons completed</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['lessons_completed'] }}</span>
                    @if ($stats['lessons_total'] > 0)
                        <span class="text-sm text-neutral/50">of {{ $stats['lessons_total'] }}</span>
                    @endif
                </div>
                <div class="mt-3 text-xs text-neutral/50">
                    @if ($stats['current_level'])
                        In this level.
                    @else
                        Start a lesson to see progress.
                    @endif
                </div>
            </div>

            {{-- Sessions completed --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Sessions completed</div>
                <div class="mt-2 text-3xl font-bold text-neutral">{{ $stats['sessions_completed'] }}</div>
                <div class="mt-3 text-xs text-neutral/50">Practical lessons attended.</div>
            </div>

            {{-- Next session --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Next session</div>
                @if ($stats['next_session'])
                    <div class="mt-2 text-2xl font-bold text-neutral">
                        {{ $stats['next_session']->scheduled_at?->format('M j · H:i') }}
                    </div>
                    <div class="mt-3 text-xs text-neutral/50">
                        with {{ $stats['next_session']->instructor?->name ?? 'instructor' }}
                    </div>
                @else
                    <div class="mt-2 text-2xl font-bold text-neutral/40">None scheduled</div>
                    <div class="mt-3 text-xs text-neutral/50">No upcoming practical sessions.</div>
                @endif
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center gap-3">
            <span class="mr-2 text-xs font-semibold uppercase tracking-[0.15em] text-neutral/50">Quick actions</span>

            <a href="{{ route('student.lessons.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/>
                    <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/>
                </svg>
                Continue learning
            </a>

            <a href="{{ route('student.practical.index') }}"
               class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                View schedule
            </a>
        </div>

    @else
        {{-- Unknown role fallback --}}
        <div class="rounded-xl border border-neutral/10 bg-white p-6">
            <p class="text-sm text-neutral/70">Your dashboard.</p>
        </div>
    @endif

</div>
@endsection
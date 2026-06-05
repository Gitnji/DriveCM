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

        {{-- Stat cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

            {{-- Students --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5 transition hover:border-neutral/20 hover:shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Students</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['students_total'] }}</span>
                    <span class="text-sm text-neutral/50">total</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">
                    <span class="font-semibold text-success">{{ $stats['students_active'] }}</span> active in last 30 days
                </div>
            </div>

            {{-- Lessons published --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5 transition hover:border-neutral/20 hover:shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Lessons published</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['lessons_published'] }}</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">Theory content live for students.</div>
            </div>

            {{-- Pages published --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5 transition hover:border-neutral/20 hover:shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Pages published</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['pages_published'] }}</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">On your public website.</div>
            </div>

            {{-- Sessions this week --}}
            <div class="rounded-xl border border-neutral/10 bg-white p-5 transition hover:border-neutral/20 hover:shadow-sm">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Sessions this week</div>
                <div class="mt-2 flex items-baseline gap-2">
                    <span class="text-3xl font-bold text-neutral">{{ $stats['sessions_week'] }}</span>
                </div>
                <div class="mt-3 text-xs text-neutral/50">Practical lessons scheduled.</div>
            </div>
        </div>

        {{-- Quick actions --}}
        <div class="mt-8 flex flex-wrap items-center gap-3">
            <span class="mr-2 text-xs font-semibold uppercase tracking-[0.15em] text-neutral/50">Quick actions</span>

            @can('author-lessons')
                <a href="{{ route('lms.lessons.create') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    New lesson
                </a>
            @endcan

            @can('manage-site')
                <a href="{{ route('site.pages.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M12 20h9"/>
                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"/>
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
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    View reports
                </a>
            @endcan
        </div>

    @else
        {{-- DASH-3 / DASH-4 placeholder — non-owner tenant roles --}}
        <div class="rounded-xl border border-neutral/10 bg-white p-6">
            <p class="text-sm text-neutral/70">
                @switch($user->role)
                    @case('secretary')
                        Student registration and scheduling tools will appear here.
                        @break
                    @case('instructor')
                        Your lessons and practical schedule will appear here.
                        @break
                    @case('student')
                        Your theory lessons and progress will appear here.
                        @break
                    @default
                        Your dashboard.
                @endswitch
            </p>
        </div>
    @endif

</div>
@endsection
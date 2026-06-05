@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<style>
    .dash-reveal {
        opacity: 0;
        transform: translateY(12px);
        transition: opacity 500ms cubic-bezier(0.16, 1, 0.3, 1), transform 500ms cubic-bezier(0.16, 1, 0.3, 1);
    }
    .dash-reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }
    .dash-card-pending {
        position: relative;
        background: linear-gradient(180deg, rgba(255, 149, 0, 0.06), rgba(255, 149, 0, 0.02));
    }
    .dash-card-pending::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        padding: 1px;
        background: linear-gradient(135deg, #FF9500, transparent 50%);
        -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: xor;
        mask-composite: exclude;
        pointer-events: none;
    }
    .dash-stat-num {
        background: linear-gradient(135deg, #0A3D62 0%, #1c5e8e 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .dash-stat-num-accent {
        background: linear-gradient(135deg, #FF9500 0%, #FFB347 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .dash-stat-num-success {
        background: linear-gradient(135deg, #00A651 0%, #4FB87E 100%);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    @media (prefers-reduced-motion: reduce) {
        .dash-reveal { transition: none; opacity: 1; transform: none; }
    }
</style>

@php
    // Sparkline helpers — compute the SVG polyline points for $trend (30 ints).
    $trendW = 280;
    $trendH = 56;
    $trendMax = max(max($trend ?? [0]), 1);
    $trendN = count($trend ?? []);
    $sparkPoints = '';
    $sparkArea = '';
    if ($trendN > 0) {
        $points = [];
        foreach ($trend as $i => $v) {
            $x = $trendN > 1 ? ($i / ($trendN - 1)) * $trendW : 0;
            $y = $trendH - ($v / $trendMax) * ($trendH - 4) - 2;
            $points[] = round($x, 2) . ',' . round($y, 2);
        }
        $sparkPoints = implode(' ', $points);
        $sparkArea = "0,{$trendH} " . $sparkPoints . " {$trendW},{$trendH}";
    }
    $trendTotal = array_sum($trend ?? []);
@endphp

<div class="mx-auto max-w-6xl">
    {{-- Heading --}}
    <div class="dash-reveal mb-10">
        <div class="text-xs font-semibold uppercase tracking-[0.18em] text-primary">Platform</div>
        <h1 class="mt-2 text-4xl font-bold tracking-tight text-primary-dark">Dashboard</h1>
    </div>

    {{-- Top stat cards: 4 across desktop, 2x2 tablet, 1x4 mobile --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">

        {{-- Pending applications --}}
        <div class="dash-reveal {{ $stats['pending'] > 0 ? 'dash-card-pending' : '' }}
                    group relative overflow-hidden rounded-2xl border {{ $stats['pending'] > 0 ? 'border-transparent' : 'border-neutral/10' }}
                    bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl">
            <div class="relative">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide {{ $stats['pending'] > 0 ? 'text-accent' : 'text-neutral/50' }}">
                    @if ($stats['pending'] > 0)
                        <span class="inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-accent"></span>
                    @endif
                    Pending applications
                </div>
                <div class="mt-3 text-5xl font-bold leading-none {{ $stats['pending'] > 0 ? 'dash-stat-num-accent' : 'dash-stat-num' }}">
                    {{ $stats['pending'] }}
                </div>
                @if ($stats['pending'] > 0)
                    <a href="{{ route('admin.applications.index') }}"
                       class="mt-5 inline-flex items-center gap-1.5 text-sm font-semibold text-accent-dark hover:text-accent">
                        Review applications
                        <span class="transition-transform group-hover:translate-x-0.5">→</span>
                    </a>
                @else
                    <div class="mt-5 text-sm text-neutral/40">No pending.</div>
                @endif
            </div>
        </div>

        {{-- Active schools --}}
        <div class="dash-reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-primary/5 transition-transform group-hover:scale-125"></div>
            <div class="relative">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Active schools</div>
                <div class="mt-3 text-5xl font-bold leading-none dash-stat-num">{{ $stats['active'] }}</div>
                <div class="mt-5 text-sm text-neutral/40">Running on DriveCM.</div>
            </div>
        </div>

        {{-- Active this week (DASH-1c.2) --}}
        <div class="dash-reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-success/8 transition-transform group-hover:scale-125"></div>
            <div class="relative">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-neutral/50">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-success"></span>
                    Active this week
                </div>
                <div class="mt-3 text-5xl font-bold leading-none dash-stat-num-success">{{ $stats['active_week'] }}</div>
                <div class="mt-5 text-sm text-neutral/40">
                    {{ $stats['active_week'] }}/{{ $stats['active'] }} signed in.
                </div>
            </div>
        </div>

        {{-- Total students --}}
        <div class="dash-reveal group relative overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-xl">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-primary/5 transition-transform group-hover:scale-125"></div>
            <div class="relative">
                <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Students</div>
                <div class="mt-3 text-5xl font-bold leading-none dash-stat-num">{{ $stats['total_students'] }}</div>
                <div class="mt-5 text-sm text-neutral/40">All schools.</div>
            </div>
        </div>
    </div>
    {{-- Quick actions (DASH-1d / D162) — below stat cards, above the lower content. --}}
    <div class="dash-reveal mt-8 flex flex-wrap items-center gap-3">
        <span class="mr-2 text-xs font-semibold uppercase tracking-[0.15em] text-neutral/50">Quick actions</span>

        <a href="{{ route('admin.applications.index') }}"
           class="group inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-accent hover:bg-accent/5 hover:text-accent-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
            </svg>
            Open pending applications
            @if ($stats['pending'] > 0)
                <span class="rounded-full bg-accent/15 px-2 py-0.5 text-xs font-bold text-accent-dark">
                    {{ $stats['pending'] }}
                </span>
            @endif
        </a>

        <a href="{{ route('admin.applications.export') }}"
           class="group inline-flex items-center gap-2 rounded-lg border border-neutral/15 bg-white px-4 py-2 text-sm font-semibold text-neutral shadow-sm transition hover:border-primary hover:bg-primary/5 hover:text-primary-dark">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export schools (CSV)
        </a>
    </div>
    {{-- Application trend (DASH-1c.4) + platform totals (DASH-1c.3) — side by side on desktop --}}
    <div class="mt-10 grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- Sparkline card (spans 2 cols on desktop) --}}
        <div class="dash-reveal overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Applications · last 30 days</div>
                    <div class="mt-2 text-3xl font-bold leading-none dash-stat-num">{{ $trendTotal }}</div>
                </div>
                <div class="text-xs text-neutral/40">{{ now()->subDays(29)->format('M j') }} — {{ now()->format('M j') }}</div>
            </div>
            <div class="mt-4">
                @if ($trendTotal > 0)
                    <svg viewBox="0 0 {{ $trendW }} {{ $trendH }}" preserveAspectRatio="none"
                         class="block h-16 w-full">
                        <defs>
                            <linearGradient id="sparkGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#0A3D62" stop-opacity="0.22"/>
                                <stop offset="100%" stop-color="#0A3D62" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                        <polygon points="{{ $sparkArea }}" fill="url(#sparkGrad)"/>
                        <polyline points="{{ $sparkPoints }}" fill="none" stroke="#0A3D62" stroke-width="2"
                                  stroke-linejoin="round" stroke-linecap="round"/>
                    </svg>
                @else
                    <div class="flex h-16 items-center justify-center text-xs text-neutral/40">
                        No applications in the last 30 days.
                    </div>
                @endif
            </div>
        </div>

        {{-- Platform totals (DASH-1c.3) --}}
        <div class="dash-reveal overflow-hidden rounded-2xl border border-neutral/10 bg-white p-6 shadow-sm">
            <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Platform activity</div>
            <div class="mt-4 space-y-3">
                <div class="flex items-baseline justify-between border-b border-neutral/5 pb-3">
                    <span class="text-sm text-neutral/60">Lessons authored</span>
                    <span class="text-lg font-bold text-neutral">{{ $platform['lessons'] }}</span>
                </div>
                <div class="flex items-baseline justify-between border-b border-neutral/5 pb-3">
                    <span class="text-sm text-neutral/60">Practical sessions</span>
                    <span class="text-lg font-bold text-neutral">{{ $platform['sessions'] }}</span>
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-sm text-neutral/60">Reports validated</span>
                    <span class="text-lg font-bold text-neutral">{{ $platform['reports'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Two recent lists side-by-side on desktop, stacked on mobile --}}
    <div class="mt-10 grid grid-cols-1 gap-5 lg:grid-cols-2">

        {{-- Recent applications --}}
        <div class="dash-reveal overflow-hidden rounded-2xl border border-neutral/10 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-neutral/10 bg-gradient-to-r from-white to-surface px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-neutral">Recent applications</h2>
                    <div class="mt-0.5 text-xs text-neutral/50">Latest 5 schools to apply.</div>
                </div>
                <a href="{{ route('admin.applications.index') }}"
                   class="group inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary-dark">
                    View all
                    <span class="transition-transform group-hover:translate-x-0.5">→</span>
                </a>
            </div>

            @if ($recent->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral/50">No applications yet.</div>
            @else
                <div class="divide-y divide-neutral/10">
                    @foreach ($recent as $tenant)
                        @php
                            $statusBadge = match ($tenant->status) {
                                'pending'  => ['bg-accent/12',  'text-accent-dark', 'Pending'],
                                'active'   => ['bg-success/12', 'text-success',     'Active'],
                                'approved' => ['bg-success/12', 'text-success',     'Approved'],
                                'rejected' => ['bg-red-100',    'text-red-700',     'Rejected'],
                                default    => ['bg-neutral/10', 'text-neutral/60',  ucfirst($tenant->status ?? 'Unknown')],
                            };
                        @endphp
                        <a href="{{ route('admin.applications.show', $tenant) }}"
                           class="group flex items-center justify-between px-6 py-4 transition hover:bg-surface">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-xs font-semibold text-white">
                                    {{ \App\Support\InstructorAvatar::initials($tenant->name ?? '?') }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-neutral group-hover:text-primary-dark">
                                        {{ $tenant->name ?? 'Untitled school' }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-neutral/50">
                                        {{ $tenant->applicant_town ?? '—' }}
                                        <span class="text-neutral/30">·</span>
                                        {{ $tenant->created_at?->diffForHumans() ?? 'unknown' }}
                                    </div>
                                </div>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                                {{ $statusBadge[2] }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Recently activated (DASH-1c.1) --}}
        <div class="dash-reveal overflow-hidden rounded-2xl border border-neutral/10 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-neutral/10 bg-gradient-to-r from-white to-surface px-6 py-5">
                <div>
                    <h2 class="text-lg font-semibold text-neutral">Recently activated</h2>
                    <div class="mt-0.5 text-xs text-neutral/50">Latest 5 schools running on DriveCM.</div>
                </div>
            </div>

            @if ($recent_active->isEmpty())
                <div class="px-6 py-12 text-center text-sm text-neutral/50">No activated schools yet.</div>
            @else
                <div class="divide-y divide-neutral/10">
                    @foreach ($recent_active as $tenant)
                        <a href="{{ route('admin.applications.show', $tenant) }}"
                           class="group flex items-center justify-between px-6 py-4 transition hover:bg-surface">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-success to-primary text-xs font-semibold text-white">
                                    {{ \App\Support\InstructorAvatar::initials($tenant->name ?? '?') }}
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-neutral group-hover:text-primary-dark">
                                        {{ $tenant->name ?? 'Untitled school' }}
                                    </div>
                                    <div class="mt-0.5 text-xs text-neutral/50">
                                        {{ $tenant->applicant_town ?? '—' }}
                                        <span class="text-neutral/30">·</span>
                                        Activated {{ $tenant->updated_at?->diffForHumans() ?? 'unknown' }}
                                    </div>
                                </div>
                            </div>
                            <span class="text-neutral/30 transition-transform group-hover:translate-x-0.5">→</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        if (! ('IntersectionObserver' in window)) {
            document.querySelectorAll('.dash-reveal').forEach((el) => el.classList.add('is-visible'));
            return;
        }
        const io = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        document.querySelectorAll('.dash-reveal').forEach((el) => io.observe(el));
    })();
</script>
@endsection
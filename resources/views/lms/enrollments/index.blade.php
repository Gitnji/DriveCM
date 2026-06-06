@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral">Student Applications</h1>
        <p class="mt-1 text-sm text-neutral/60">Review and approve enrollment applications.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Pending applications --}}
    <div class="rounded-xl border border-neutral/10 bg-white">
        <div class="flex items-center justify-between border-b border-neutral/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-neutral">Pending</h2>
            <span class="rounded-full bg-accent/15 px-2.5 py-0.5 text-xs font-bold text-accent-dark">
                {{ $pending->count() }}
            </span>
        </div>

        @if ($pending->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-neutral/50">No pending applications.</div>
        @else
            <div class="divide-y divide-neutral/10">
                @foreach ($pending as $app)
                    <a href="{{ route('lms.enrollments.show', $app) }}"
                       class="group flex items-center justify-between px-6 py-4 transition hover:bg-surface">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-sm font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($app->name) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral group-hover:text-primary-dark">
                                    {{ $app->name }}
                                </div>
                                <div class="mt-0.5 text-xs text-neutral/50">
                                    {{ $app->email }} · {{ $app->phone ?? '—' }}
                                    <span class="text-neutral/30">·</span>
                                    {{ $app->town }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full bg-neutral/8 px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-neutral/60">
                                {{ $app->source === 'public_form' ? 'Public' : 'In-person' }}
                            </span>
                            <span class="text-xs text-neutral/50">{{ $app->submitted_at?->diffForHumans() ?? '—' }}</span>
                            <span class="text-neutral/30 transition-transform group-hover:translate-x-0.5">→</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Recent decisions --}}
    <div class="mt-8 rounded-xl border border-neutral/10 bg-white">
        <div class="border-b border-neutral/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-neutral">Recent decisions</h2>
            <p class="mt-0.5 text-xs text-neutral/50">Last 10 approved or rejected applications.</p>
        </div>

        @if ($recent->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-neutral/50">No decisions yet.</div>
        @else
            <div class="divide-y divide-neutral/10">
                @foreach ($recent as $app)
                    @php
                        $statusBadge = match ($app->status) {
                            'approved' => ['bg-success/15', 'text-success', 'Approved'],
                            'rejected' => ['bg-red-100',    'text-red-700', 'Rejected'],
                            default    => ['bg-neutral/10', 'text-neutral/60', ucfirst($app->status)],
                        };
                    @endphp
                    <a href="{{ route('lms.enrollments.show', $app) }}"
                       class="group flex items-center justify-between px-6 py-4 transition hover:bg-surface">
                        <div class="flex items-center gap-4">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-neutral/30 to-neutral/50 text-xs font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($app->name) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral group-hover:text-primary-dark">
                                    {{ $app->name }}
                                </div>
                                <div class="mt-0.5 text-xs text-neutral/50">
                                    {{ $app->reviewed_at?->diffForHumans() ?? '—' }}
                                    @if ($app->reviewer)
                                        <span class="text-neutral/30">·</span> by {{ $app->reviewer->name }}
                                    @endif
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
</div>
@endsection
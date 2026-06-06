@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('lms.enrollments.index') }}" class="text-sm font-medium text-primary hover:underline">
        ← Student Applications
    </a>

    <div class="mt-4 rounded-xl border border-neutral/10 bg-white p-6">
        <div class="flex items-start justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-lg font-semibold text-white">
                    {{ \App\Support\InstructorAvatar::initials($application->name) }}
                </div>
                <div>
                    <h1 class="text-xl font-semibold text-neutral">{{ $application->name }}</h1>
                    <p class="mt-0.5 text-xs text-neutral/50">
                        Submitted {{ $application->submitted_at?->diffForHumans() ?? '—' }}
                        · {{ $application->source === 'public_form' ? 'Public form' : 'In-person entry' }}
                    </p>
                </div>
            </div>
            @php
                $statusBadge = match ($application->status) {
                    'pending'  => ['bg-accent/15',  'text-accent-dark', 'Pending'],
                    'approved' => ['bg-success/15', 'text-success',     'Approved'],
                    'rejected' => ['bg-red-100',    'text-red-700',     'Rejected'],
                };
            @endphp
            <span class="rounded-full px-3 py-1 text-sm font-semibold {{ $statusBadge[0] }} {{ $statusBadge[1] }}">
                {{ $statusBadge[2] }}
            </span>
        </div>

        {{-- Applicant info --}}
        <dl class="mt-6 grid grid-cols-1 gap-x-6 gap-y-4 border-t border-neutral/10 pt-6 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Email</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $application->email }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Phone</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $application->phone ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Town</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $application->town }}</dd>
            </div>
            @if ($application->notes)
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Notes</dt>
                    <dd class="mt-1 whitespace-pre-line text-sm text-neutral">{{ $application->notes }}</dd>
                </div>
            @endif
        </dl>

        @if ($application->status === 'pending')
            {{-- Approve/Reject actions --}}
            <div class="mt-8 grid grid-cols-1 gap-4 border-t border-neutral/10 pt-6 sm:grid-cols-2">
                {{-- Approve --}}
                <form method="POST" action="{{ route('lms.enrollments.approve', $application) }}">
                    @csrf
                    <button type="submit"
                            class="w-full rounded-lg bg-success px-4 py-3 text-sm font-semibold text-white hover:bg-success/90">
                        Approve & create student account
                    </button>
                </form>

                {{-- Reject --}}
                <form method="POST" action="{{ route('lms.enrollments.reject', $application) }}" class="space-y-2">
                    @csrf
                    <input type="text" name="rejection_reason"
                           placeholder="Reason (optional)" maxlength="500"
                           class="w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                    <button type="submit"
                            class="w-full rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700 hover:bg-red-100">
                        Reject
                    </button>
                </form>
            </div>
        @elseif ($application->status === 'rejected' && $application->rejection_reason)
            <div class="mt-6 border-t border-neutral/10 pt-6">
                <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Rejection reason</dt>
                <dd class="mt-1 text-sm text-neutral">{{ $application->rejection_reason }}</dd>
            </div>
        @endif

        @if ($application->reviewer && $application->status !== 'pending')
            <div class="mt-6 border-t border-neutral/10 pt-4 text-xs text-neutral/50">
                Reviewed {{ $application->reviewed_at?->diffForHumans() }} by {{ $application->reviewer->name }}
            </div>
        @endif
    </div>
</div>
@endsection
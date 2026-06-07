@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral">Payment Reviews</h1>
            <p class="mt-1 text-sm text-neutral/60">Review student-submitted payment proofs.</p>
        </div>
        <a href="{{ route('lms.payment-reviews.manual.create') }}"
           class="rounded-lg border border-neutral/20 bg-white px-4 py-2 text-sm font-semibold text-neutral hover:bg-surface">
            + Mark cash payment
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Summary stats --}}
    <div class="mb-6 grid grid-cols-3 gap-3">
        <div class="rounded-xl border border-accent/30 bg-accent/5 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-accent-dark">Pending</div>
            <div class="mt-1 text-2xl font-bold text-neutral">{{ $pending->count() }}</div>
        </div>
        <div class="rounded-xl border border-success/20 bg-success/5 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-success">Approved</div>
            <div class="mt-1 text-2xl font-bold text-neutral">{{ $approvedCount }}</div>
        </div>
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-red-700">Rejected</div>
            <div class="mt-1 text-2xl font-bold text-neutral">{{ $rejectedCount }}</div>
        </div>
    </div>

    {{-- Pending queue --}}
    <div class="rounded-xl border border-neutral/10 bg-white">
        <div class="border-b border-neutral/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-neutral">Pending review</h2>
            <p class="mt-0.5 text-xs text-neutral/50">Oldest first.</p>
        </div>

        @if ($pending->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-neutral/50">
                No payments awaiting review.
            </div>
        @else
            <div class="divide-y divide-neutral/10">
                @foreach ($pending as $payment)
                    <a href="{{ route('lms.payment-reviews.show', $payment) }}"
                       class="group flex items-center justify-between px-6 py-4 transition hover:bg-surface">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-sm font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($payment->student?->name ?? '?') }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral group-hover:text-primary-dark">{{ $payment->student?->name ?? '—' }}</div>
                                <div class="mt-0.5 text-xs text-neutral/50">
                                    {{ $payment->paymentType?->name ?? '—' }}
                                    <span class="text-neutral/30">·</span>
                                    {{ number_format($payment->amount_xaf) }} XAF
                                    <span class="text-neutral/30">·</span>
                                    Submitted {{ $payment->submitted_at?->diffForHumans() ?? '' }}
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
@endsection
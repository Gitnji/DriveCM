@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl">
    <a href="{{ route('lms.payment-reviews.index') }}" class="text-sm font-medium text-primary hover:underline">← Reviews</a>

    <div class="mt-4 flex items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-neutral">Review payment</h1>
            <p class="mt-1 text-sm text-neutral/60">{{ $payment->student?->name ?? '—' }} · {{ $payment->paymentType?->name ?? '—' }}</p>
        </div>
        <div class="text-right">
            <div class="text-xl font-bold text-neutral">{{ number_format($payment->amount_xaf) }}</div>
            <div class="text-xs text-neutral/50">XAF</div>
        </div>
    </div>

    @if ($payment->status !== 'pending_review')
        <div class="mt-6 rounded-xl border border-neutral/10 bg-neutral/5 p-4">
            @if ($payment->isApproved())
                <div class="text-sm font-semibold text-success">✓ Approved</div>
            @elseif ($payment->isRejected())
                <div class="text-sm font-semibold text-red-700">✗ Rejected</div>
                @if ($payment->rejection_reason)
                    <p class="mt-1 text-xs text-neutral/70">Reason: {{ $payment->rejection_reason }}</p>
                @endif
            @endif
            <p class="mt-1 text-xs text-neutral/50">
                Reviewed {{ $payment->reviewed_at?->diffForHumans() ?? '' }}
                @if ($payment->reviewer)
                    by {{ $payment->reviewer->name }}
                @endif
            </p>
        </div>
    @endif

    {{-- Student info --}}
    <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Student</h2>
        <dl class="mt-3 grid grid-cols-1 gap-2 sm:grid-cols-2">
            <div>
                <dt class="text-xs text-neutral/50">Name</dt>
                <dd class="text-sm font-medium text-neutral">{{ $payment->student?->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-neutral/50">Email</dt>
                <dd class="text-sm font-medium text-neutral">{{ $payment->student?->email }}</dd>
            </div>
            @if ($payment->student?->phone)
                <div>
                    <dt class="text-xs text-neutral/50">Phone</dt>
                    <dd class="text-sm font-medium text-neutral">{{ $payment->student->phone }}</dd>
                </div>
            @endif
            @if ($payment->student?->town)
                <div>
                    <dt class="text-xs text-neutral/50">Town</dt>
                    <dd class="text-sm font-medium text-neutral">{{ $payment->student->town }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Screenshot --}}
    <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-5">
        <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Payment proof</h2>
        @if ($payment->screenshot)
            <a href="{{ route('lms.uploads.show', $payment->screenshot) }}" target="_blank" rel="noopener" class="mt-3 block">
                <img src="{{ route('lms.uploads.show', $payment->screenshot) }}" alt="Payment screenshot"
                     class="max-h-[600px] w-full rounded-lg border border-neutral/10 object-contain">
            </a>
            <p class="mt-2 text-xs text-neutral/50">Click to open full size.</p>
        @else
            <p class="mt-3 text-xs text-neutral/50">No screenshot (manual entry).</p>
        @endif
    </div>

    {{-- Notes --}}
    @if ($payment->notes)
        <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Student notes</h2>
            <p class="mt-3 whitespace-pre-line text-sm text-neutral/80">{{ $payment->notes }}</p>
        </div>
    @endif

    {{-- Actions: only show if still pending --}}
    @if ($payment->status === 'pending_review')
        <div class="mt-8 space-y-3">
            {{-- Approve --}}
            <form method="POST" action="{{ route('lms.payment-reviews.approve', $payment) }}"
                  onsubmit="return confirm('Approve this payment? The student will be able to access lessons immediately.');">
                @csrf
                <button type="submit"
                        class="w-full rounded-lg bg-success px-4 py-3 text-sm font-semibold text-white hover:opacity-90">
                    ✓ Approve payment
                </button>
            </form>

            {{-- Reject --}}
            <form method="POST" action="{{ route('lms.payment-reviews.reject', $payment) }}" class="space-y-2">
                @csrf
                <textarea name="rejection_reason" rows="2" maxlength="500"
                          class="w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-red-300 focus:ring-1 focus:ring-red-300"
                          placeholder="Reason for rejection (shown to student, optional but recommended)"></textarea>
                <button type="submit"
                        onclick="return confirm('Reject this payment? The student will see your reason and can re-submit.');"
                        class="w-full rounded-lg border border-red-300 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700 hover:bg-red-100">
                    ✗ Reject payment
                </button>
            </form>
        </div>
    @endif
</div>
@endsection
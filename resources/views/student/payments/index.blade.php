@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold text-neutral">My Payments</h1>
    <p class="mt-1 text-sm text-neutral/60">Submit proof of payment to keep learning.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- ====== School receiving accounts (D170 / P2) ====== --}}
    @php
        $hasReceivingInfo = $tenant->momo_number || $tenant->orange_number || $tenant->payment_instructions;
    @endphp

    @if ($hasReceivingInfo)
        <div class="mt-6 rounded-xl border border-primary/20 bg-primary/5 p-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-primary-dark">Send payment to</h2>

            <dl class="mt-3 space-y-2">
                @if ($tenant->momo_number)
                    <div class="flex items-baseline gap-2 text-sm">
                        <dt class="font-medium text-neutral/70 w-24 shrink-0">MTN MoMo:</dt>
                        <dd class="font-mono font-semibold text-neutral select-all">{{ $tenant->momo_number }}</dd>
                    </div>
                @endif
                @if ($tenant->orange_number)
                    <div class="flex items-baseline gap-2 text-sm">
                        <dt class="font-medium text-neutral/70 w-24 shrink-0">Orange:</dt>
                        <dd class="font-mono font-semibold text-neutral select-all">{{ $tenant->orange_number }}</dd>
                    </div>
                @endif
            </dl>

            @if ($tenant->payment_instructions)
                <div class="mt-4 border-t border-primary/15 pt-3">
                    <div class="whitespace-pre-line text-xs text-neutral/70">{{ $tenant->payment_instructions }}</div>
                </div>
            @endif
        </div>
    @else
        <div class="mt-6 rounded-xl border border-accent/30 bg-accent/5 p-4">
            <div class="text-sm font-semibold text-accent-dark">Payment information not configured</div>
            <p class="mt-1 text-xs text-neutral/70">
                Your school hasn't set up payment receiving accounts yet. Please contact the school directly to arrange payment.
            </p>
        </div>
    @endif

    {{-- ====== Required pending — top of page, most urgent ====== --}}
    @if ($pendingRequired->isNotEmpty())
        <h2 class="mt-8 text-sm font-semibold uppercase tracking-wide text-neutral/50">Required payments</h2>

        <div class="mt-3 space-y-3">
            @foreach ($pendingRequired as $type)
                @php
                    $previousAttempts = $paymentsByType->get($type->id, collect());
                    $rejected = $previousAttempts->first(fn ($p) => $p->isRejected());
                    $pendingReview = $previousAttempts->first(fn ($p) => $p->isPending());
                @endphp
                <div class="rounded-xl border border-red-200 bg-red-50/40 p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-base font-semibold text-neutral">{{ $type->name }}</div>
                            @if ($type->description)
                                <p class="mt-1 text-xs text-neutral/60">{{ $type->description }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right">
                            <div class="text-lg font-semibold text-neutral">{{ number_format($type->amount_xaf) }}</div>
                            <div class="text-xs text-neutral/50">XAF</div>
                        </div>
                    </div>

                    @if ($pendingReview)
                        {{-- Already submitted, awaiting review --}}
                        <div class="mt-4 rounded-lg border border-accent/30 bg-accent/10 p-3">
                            <div class="text-xs font-semibold text-accent-dark">⏳ Pending review</div>
                            <p class="mt-1 text-xs text-neutral/70">
                                Submitted {{ $pendingReview->submitted_at?->diffForHumans() ?? 'recently' }}. The school will review your payment proof.
                            </p>
                        </div>
                    @elseif ($rejected)
                        {{-- Previously rejected — show reason + re-submit form --}}
                        <div class="mt-4 rounded-lg border border-red-300 bg-red-100/60 p-3">
                            <div class="text-xs font-semibold text-red-900">✗ Previous submission rejected</div>
                            @if ($rejected->rejection_reason)
                                <p class="mt-1 text-xs text-red-800/80">Reason: {{ $rejected->rejection_reason }}</p>
                            @endif
                            <p class="mt-1 text-xs text-red-800/80">Please re-submit with a clearer screenshot below.</p>
                        </div>
                        @include('student.payments.partials.submit-form', ['type' => $type])
                    @else
                        {{-- Fresh — no prior attempt --}}
                        @include('student.payments.partials.submit-form', ['type' => $type])
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    {{-- ====== Approved payments — history ====== --}}
    @php
        $approvedPayments = $allPayments->filter(fn ($p) => $p->isApproved());
    @endphp
    @if ($approvedPayments->isNotEmpty())
        <h2 class="mt-8 text-sm font-semibold uppercase tracking-wide text-neutral/50">Approved payments</h2>

        <div class="mt-3 space-y-2">
            @foreach ($approvedPayments as $payment)
                <div class="rounded-xl border border-success/20 bg-success/5 p-4">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <div class="text-sm font-semibold text-neutral">{{ $payment->paymentType?->name ?? '—' }}</div>
                            <div class="mt-0.5 text-xs text-neutral/50">
                                Approved {{ $payment->reviewed_at?->diffForHumans() ?? '' }}
                                @if ($payment->reviewer)
                                    by {{ $payment->reviewer->name }}
                                @endif
                                @if ($payment->isManualMark())
                                    <span class="ml-1 rounded-full bg-neutral/10 px-2 py-0.5 text-xs text-neutral/60">Cash</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-success">{{ number_format($payment->amount_xaf) }} XAF</div>
                            <div class="text-xs text-success/70">✓ Paid</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ====== Optional types — bottom, voluntary opt-in ====== --}}
    @php
        // Filter out optional types that already have a pending or approved payment.
        $optionalToShow = $optionalTypes->filter(function ($type) use ($paymentsByType) {
            $attempts = $paymentsByType->get($type->id, collect());
            return ! $attempts->contains(fn ($p) => $p->isApproved() || $p->isPending());
        });
    @endphp
    @if ($optionalToShow->isNotEmpty())
        <h2 class="mt-8 text-sm font-semibold uppercase tracking-wide text-neutral/50">Optional payments</h2>
        <p class="mt-1 text-xs text-neutral/50">These are optional services. Pay only if you want them.</p>

        <div class="mt-3 space-y-3">
            @foreach ($optionalToShow as $type)
                <div class="rounded-xl border border-neutral/10 bg-white p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-base font-semibold text-neutral">{{ $type->name }}</div>
                            @if ($type->description)
                                <p class="mt-1 text-xs text-neutral/60">{{ $type->description }}</p>
                            @endif
                        </div>
                        <div class="shrink-0 text-right">
                            <div class="text-lg font-semibold text-neutral">{{ number_format($type->amount_xaf) }}</div>
                            <div class="text-xs text-neutral/50">XAF</div>
                        </div>
                    </div>
                    @include('student.payments.partials.submit-form', ['type' => $type])
                </div>
            @endforeach
        </div>
    @endif

    {{-- ====== Empty state — paid up and nothing optional ====== --}}
    @if ($pendingRequired->isEmpty() && $approvedPayments->isEmpty() && $optionalToShow->isEmpty())
        <div class="mt-8 rounded-xl border border-dashed border-neutral/20 p-8 text-center">
            <div class="text-sm text-neutral/60">No payments needed at this time.</div>
            <p class="mt-1 text-xs text-neutral/40">You're all set. Keep learning!</p>
        </div>
    @endif
</div>
@endsection
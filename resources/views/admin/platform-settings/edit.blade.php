@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold text-neutral">Platform Settings</h1>
    <p class="mt-1 text-sm text-neutral/60">Platform-wide pricing, trial duration, and the receiving accounts schools pay to.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.platform-settings.update') }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf @method('PUT')

        {{-- Pricing section --}}
        <div class="border-b border-neutral/10 pb-5">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Pricing</h2>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral" for="monthly_fee_xaf">Monthly fee per school (XAF)</label>
                    <input type="number" id="monthly_fee_xaf" name="monthly_fee_xaf" value="{{ old('monthly_fee_xaf', $settings->monthly_fee_xaf) }}" required min="0" max="10000000"
                           class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    <p class="mt-1 text-xs text-neutral/50">What each school pays per month. Set to 0 to temporarily disable billing platform-wide.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral" for="free_trial_days">Free trial (days from approval)</label>
                    <input type="number" id="free_trial_days" name="free_trial_days" value="{{ old('free_trial_days', $settings->free_trial_days) }}" required min="0" max="365"
                           class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    <p class="mt-1 text-xs text-neutral/50">New schools get this many days free after their tenant is approved. Set to 0 to bill immediately.</p>
                </div>
            </div>
        </div>

        {{-- Receiving accounts section --}}
        <div>
            <h2 class="text-sm font-semibold uppercase tracking-wide text-neutral/50">Receiving accounts</h2>
            <p class="mt-1 text-xs text-neutral/50">Where schools send their platform payments. Shown to owners on their billing page.</p>

            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-neutral" for="momo_number">MTN MoMo number</label>
                    <input type="tel" id="momo_number" name="momo_number" value="{{ old('momo_number', $settings->momo_number) }}" maxlength="40"
                           class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                           placeholder="+237 6XX XXX XXX">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral" for="orange_number">Orange Money number</label>
                    <input type="tel" id="orange_number" name="orange_number" value="{{ old('orange_number', $settings->orange_number) }}" maxlength="40"
                           class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                           placeholder="+237 6XX XXX XXX">
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral" for="payment_instructions">Payment instructions</label>
                    <textarea id="payment_instructions" name="payment_instructions" rows="4" maxlength="2000"
                              class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                              placeholder="Free-text guidance shown to owners on the billing page.">{{ old('payment_instructions', $settings->payment_instructions) }}</textarea>
                </div>
            </div>
        </div>

        <div class="border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Save settings
            </button>
        </div>
    </form>

    {{-- Read-only info card --}}
    <div class="mt-6 rounded-lg border border-neutral/10 bg-surface p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Current pricing</div>
        <div class="mt-2 text-lg font-bold text-neutral">{{ number_format($settings->monthly_fee_xaf) }} XAF / month</div>
        <div class="mt-1 text-xs text-neutral/60">{{ $settings->free_trial_days }}-day free trial for new schools.</div>
    </div>
</div>
@endsection
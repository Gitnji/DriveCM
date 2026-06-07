@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold text-neutral">Receiving Accounts</h1>
    <p class="mt-1 text-sm text-neutral/60">Where students send money. These are displayed on the student Payments page when they're prompted to pay.</p>

    @if (session('status'))
        <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.payment-settings.update') }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-neutral" for="momo_number">MTN MoMo number <span class="text-neutral/40">(optional)</span></label>
            <input type="tel" id="momo_number" name="momo_number" value="{{ old('momo_number', $tenant->momo_number) }}" maxlength="40"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="+237 6XX XXX XXX">
            <p class="mt-1 text-xs text-neutral/50">The number students should send MoMo payments to.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="orange_number">Orange Money number <span class="text-neutral/40">(optional)</span></label>
            <input type="tel" id="orange_number" name="orange_number" value="{{ old('orange_number', $tenant->orange_number) }}" maxlength="40"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="+237 6XX XXX XXX">
            <p class="mt-1 text-xs text-neutral/50">The number students should send Orange Money payments to.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="payment_instructions">Payment instructions <span class="text-neutral/40">(optional)</span></label>
            <textarea id="payment_instructions" name="payment_instructions" rows="4" maxlength="2000"
                      class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                      placeholder="Use the student's full name as the transfer reference. Send the receipt screenshot via the Payments page.">{{ old('payment_instructions', $tenant->payment_instructions) }}</textarea>
            <p class="mt-1 text-xs text-neutral/50">Free-text guidance shown to students along with the numbers above.</p>
        </div>

        <div class="flex gap-3 border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Save settings
            </button>
        </div>
    </form>

    <div class="mt-6 rounded-lg border border-accent/20 bg-accent/5 p-4">
        <div class="text-xs font-semibold uppercase tracking-wide text-accent-dark">⚠ Heads up</div>
        <p class="mt-1 text-xs text-neutral/70">
            At least one method (MoMo or Orange Money) should be configured before students are auto-prompted to pay. Otherwise the Payments page will show "Payment information not configured."
        </p>
    </div>
</div>
@endsection
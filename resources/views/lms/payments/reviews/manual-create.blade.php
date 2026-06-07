@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('lms.payment-reviews.index') }}" class="text-sm font-medium text-primary hover:underline">← Reviews</a>

    <h1 class="mt-3 text-2xl font-bold text-neutral">Mark cash payment</h1>
    <p class="mt-1 text-sm text-neutral/60">Record a payment received in cash or outside the screenshot upload flow. This creates an approved payment record immediately.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.payment-reviews.manual.store') }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-neutral" for="student_id">Student</label>
            <select id="student_id" name="student_id" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">Select student</option>
                @foreach ($students as $student)
                    <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>{{ $student->name }} ({{ $student->email }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="payment_type_id">Payment type</label>
            <select id="payment_type_id" name="payment_type_id" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">Select type</option>
                @foreach ($types as $type)
                    <option value="{{ $type->id }}" @selected(old('payment_type_id') == $type->id)>
                        {{ $type->name }} — {{ number_format($type->amount_xaf) }} XAF
                        @if (! $type->is_required) (Optional) @endif
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="notes">Notes <span class="text-neutral/40">(optional)</span></label>
            <textarea id="notes" name="notes" rows="3" maxlength="500"
                      class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                      placeholder="e.g., Paid in cash at office on 7 June 2026. Receipt #12345.">{{ old('notes') }}</textarea>
        </div>

        <div class="flex gap-3 border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Record payment as paid
            </button>
            <a href="{{ route('lms.payment-reviews.index') }}"
               class="rounded-lg border border-neutral/20 px-5 py-2.5 text-sm font-semibold text-neutral hover:bg-surface">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('lms.payment-types.index') }}" class="text-sm font-medium text-primary hover:underline">← Payment Types</a>

    <h1 class="mt-3 text-2xl font-bold text-neutral">Edit payment type</h1>
    <p class="mt-1 text-sm text-neutral/60">Changes apply to future student payments. Existing payment records keep their original amount.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.payment-types.update', $type) }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-neutral" for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $type->name) }}" required maxlength="120"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="amount_xaf">Amount (XAF)</label>
            <input type="number" id="amount_xaf" name="amount_xaf" value="{{ old('amount_xaf', $type->amount_xaf) }}" required min="1" max="50000000"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="description">Description <span class="text-neutral/40">(optional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="2000"
                      class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">{{ old('description', $type->description) }}</textarea>
        </div>

        <div class="border-t border-neutral/10 pt-5">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="is_required" value="1" {{ old('is_required', $type->is_required) ? 'checked' : '' }}
                       class="mt-0.5 rounded border-neutral/30 text-primary focus:ring-primary">
                <div>
                    <div class="text-sm font-medium text-neutral">Required payment</div>
                    <div class="mt-0.5 text-xs text-neutral/60">Auto-prompted to students at the threshold below.</div>
                </div>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="levels_required_before_prompt">Prompt after completing level <span class="text-neutral/40">(only if required)</span></label>
            <input type="number" id="levels_required_before_prompt" name="levels_required_before_prompt" value="{{ old('levels_required_before_prompt', $type->levels_required_before_prompt) }}" min="0" max="20"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="sort_order">Sort order</label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $type->sort_order) }}" min="0"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div class="border-t border-neutral/10 pt-5">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $type->is_active) ? 'checked' : '' }}
                       class="mt-0.5 rounded border-neutral/30 text-primary focus:ring-primary">
                <div>
                    <div class="text-sm font-medium text-neutral">Active</div>
                    <div class="mt-0.5 text-xs text-neutral/60">Uncheck to hide this type from students without removing the record. Existing payment records are unaffected.</div>
                </div>
            </label>
        </div>

        <div class="flex gap-3 border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Save changes
            </button>
            <a href="{{ route('lms.payment-types.index') }}"
               class="rounded-lg border border-neutral/20 px-5 py-2.5 text-sm font-semibold text-neutral hover:bg-surface">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
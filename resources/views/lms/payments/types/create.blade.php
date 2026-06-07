@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('lms.payment-types.index') }}" class="text-sm font-medium text-primary hover:underline">← Payment Types</a>

    <h1 class="mt-3 text-2xl font-bold text-neutral">Add payment type</h1>
    <p class="mt-1 text-sm text-neutral/60">Define a fee your school charges. Required types are auto-prompted to students once they complete the configured number of levels.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.payment-types.store') }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-neutral" for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="120"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="Enrollment fee">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="amount_xaf">Amount (XAF)</label>
            <input type="number" id="amount_xaf" name="amount_xaf" value="{{ old('amount_xaf') }}" required min="1" max="50000000"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="50000">
            <p class="mt-1 text-xs text-neutral/50">Whole XAF amount. Example: 50000 for 50,000 XAF.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="description">Description <span class="text-neutral/40">(optional)</span></label>
            <textarea id="description" name="description" rows="3" maxlength="2000"
                      class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                      placeholder="Optional note shown to students on the Payments page.">{{ old('description') }}</textarea>
        </div>

        <div class="border-t border-neutral/10 pt-5">
            <label class="flex items-start gap-3">
                <input type="checkbox" name="is_required" value="1" {{ old('is_required') ? 'checked' : '' }}
                       class="mt-0.5 rounded border-neutral/30 text-primary focus:ring-primary">
                <div>
                    <div class="text-sm font-medium text-neutral">Required payment</div>
                    <div class="mt-0.5 text-xs text-neutral/60">When checked, students will be auto-prompted to pay this type after completing the threshold below. Until paid, all lessons are blocked.</div>
                </div>
            </label>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="levels_required_before_prompt">Prompt after completing level <span class="text-neutral/40">(only if required)</span></label>
            <input type="number" id="levels_required_before_prompt" name="levels_required_before_prompt" value="{{ old('levels_required_before_prompt', 2) }}" min="0" max="20"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            <p class="mt-1 text-xs text-neutral/50">Example: 2 means the student must finish the first 2 levels before being prompted. Set to 0 for immediate prompt. Ignored for optional types.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="sort_order">Sort order <span class="text-neutral/40">(optional)</span></label>
            <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            <p class="mt-1 text-xs text-neutral/50">Lower numbers appear first.</p>
        </div>

        <div class="flex gap-3 border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Create type
            </button>
            <a href="{{ route('lms.payment-types.index') }}"
               class="rounded-lg border border-neutral/20 px-5 py-2.5 text-sm font-semibold text-neutral hover:bg-surface">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
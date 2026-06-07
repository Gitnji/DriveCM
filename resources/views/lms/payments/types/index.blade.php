@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral">Payment Types</h1>
            <p class="mt-1 text-sm text-neutral/60">Configure the fees your school charges. Required types prompt students after a set number of levels; optional types appear on the Payments page for opt-in.</p>
        </div>
        <a href="{{ route('lms.payment-types.create') }}"
           class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            + Add type
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Active --}}
    <div class="rounded-xl border border-neutral/10 bg-white">
        <div class="border-b border-neutral/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-neutral">Active</h2>
        </div>

        @if ($active->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-neutral/50">
                No payment types yet. Click "+ Add type" to define your first fee.
            </div>
        @else
            <div class="divide-y divide-neutral/10">
                @foreach ($active as $type)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-semibold text-neutral">{{ $type->name }}</span>
                                @if ($type->is_required)
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-semibold text-primary-dark">Required</span>
                                @else
                                    <span class="rounded-full bg-neutral/8 px-2 py-0.5 text-xs font-semibold text-neutral/60">Optional</span>
                                @endif
                                @if (! $type->is_active)
                                    <span class="rounded-full bg-accent/10 px-2 py-0.5 text-xs font-semibold text-accent-dark">Inactive</span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-neutral/50">
                                <span class="font-medium text-neutral/80">{{ number_format($type->amount_xaf) }} XAF</span>
                                @if ($type->is_required && $type->levels_required_before_prompt !== null)
                                    · Prompted after level {{ $type->levels_required_before_prompt }}
                                @endif
                                @if ($type->description)
                                    · {{ \Illuminate\Support\Str::limit($type->description, 80) }}
                                @endif
                            </div>
                        </div>
                        <div class="ml-4 flex items-center gap-3">
                            <a href="{{ route('lms.payment-types.edit', $type) }}" class="text-sm font-medium text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('lms.payment-types.destroy', $type) }}"
                                  onsubmit="return confirm('Remove this payment type? It will not be auto-prompted to new students. Existing payment records are preserved.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Removed (soft-deleted) --}}
    @if ($deleted->isNotEmpty())
        <div class="mt-8 rounded-xl border border-neutral/10 bg-white">
            <div class="border-b border-neutral/10 px-6 py-4">
                <h2 class="text-lg font-semibold text-neutral">Removed</h2>
                <p class="mt-0.5 text-xs text-neutral/50">Soft-deleted. Restore to re-enable.</p>
            </div>
            <div class="divide-y divide-neutral/10">
                @foreach ($deleted as $type)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div>
                            <div class="text-sm font-semibold text-neutral/50 line-through">{{ $type->name }}</div>
                            <div class="mt-1 text-xs text-neutral/40">
                                {{ number_format($type->amount_xaf) }} XAF · Removed {{ $type->deleted_at?->diffForHumans() ?? 'unknown' }}
                            </div>
                        </div>
                        <form method="POST" action="{{ route('lms.payment-types.restore', $type->id) }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-primary hover:underline">Restore</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
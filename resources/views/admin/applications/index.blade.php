@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-xl font-semibold text-neutral">School Applications</h1>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <h2 class="mt-6 text-sm font-semibold text-neutral/70">Pending review</h2>
        <div class="mt-2 space-y-2">
            @forelse ($pending as $tenant)
                <a href="{{ route('admin.applications.show', $tenant) }}"
                   class="block rounded-xl border border-accent/30 bg-accent/5 p-4 hover:bg-accent/10">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-medium text-neutral">{{ $tenant->name }}</div>
                            <div class="mt-1 text-xs text-neutral/60">
                                {{ $tenant->contact_name }} · {{ $tenant->applicant_town }} ·
                                proposed <span class="font-mono">{{ $tenant->desired_subdomain }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 text-xs text-neutral/40">
                            {{ $tenant->submitted_at?->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-6 text-center text-sm text-neutral/50">
                    No applications waiting.
                </p>
            @endforelse
        </div>

        <h2 class="mt-8 text-sm font-semibold text-neutral/70">Recently reviewed</h2>
        <div class="mt-2 space-y-2">
            @forelse ($recent as $tenant)
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="text-sm font-medium text-neutral">{{ $tenant->name }}</div>
                            <div class="mt-1 text-xs text-neutral/60">
                                @if ($tenant->status === 'active')
                                    <span class="font-mono">{{ $tenant->subdomain }}.drivecm.cm</span>
                                @else
                                    {{ $tenant->rejection_reason ?: 'Rejected — no reason given' }}
                                @endif
                            </div>
                        </div>
                        @php($badge = ['active'=>'bg-success/10 text-success','rejected'=>'bg-red-50 text-red-600'][$tenant->status])
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ ucfirst($tenant->status) }}</span>
                    </div>
                </div>
            @empty
                <p class="text-xs text-neutral/40">No reviewed applications yet.</p>
            @endforelse
        </div>
    </div>
@endsection
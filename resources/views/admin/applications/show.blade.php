@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('admin.applications.index') }}" class="text-sm font-medium text-primary hover:underline">← Applications</a>

        <h1 class="mt-3 text-xl font-semibold text-neutral">{{ $tenant->name }}</h1>
        <p class="mt-1 text-sm text-neutral/60">Submitted {{ $tenant->submitted_at?->format('j M Y, H:i') }}</p>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-5">
            <h2 class="text-sm font-semibold text-neutral">Applicant</h2>
            <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                <dt class="text-neutral/50">Name</dt><dd>{{ $tenant->contact_name }}</dd>
                <dt class="text-neutral/50">Email</dt><dd>{{ $tenant->contact_email }}</dd>
                <dt class="text-neutral/50">Phone</dt><dd>{{ $tenant->contact_phone ?: '—' }}</dd>
                <dt class="text-neutral/50">Town</dt><dd>{{ $tenant->applicant_town }}</dd>
                <dt class="text-neutral/50">Proposed subdomain</dt><dd class="font-mono">{{ $tenant->desired_subdomain }}</dd>
            </dl>
        </div>

        @if ($tenant->status === 'pending')
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                {{-- Approve --}}
                <form method="POST" action="{{ route('admin.applications.approve', $tenant) }}"
                      class="rounded-xl border border-success/30 bg-success/5 p-5">
                    @csrf
                    <h3 class="text-sm font-semibold text-neutral">Approve</h3>
                    <p class="mt-1 text-xs text-neutral/60">Activates the tenant, creates the owner, seeds levels.</p>
                    <label class="mt-3 block text-xs font-medium text-neutral">Subdomain</label>
                    <input type="text" name="subdomain" value="{{ old('subdomain', $tenant->desired_subdomain) }}" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm font-mono">
                    <p class="mt-1 text-xs text-neutral/50">You may keep or change the proposed value.</p>
                    <button type="submit"
                        class="mt-3 w-full rounded-lg bg-success px-4 py-2 text-sm font-semibold text-white hover:opacity-90">
                        Approve application
                    </button>
                </form>

                {{-- Reject --}}
                <form method="POST" action="{{ route('admin.applications.reject', $tenant) }}"
                      class="rounded-xl border border-red-200 bg-red-50 p-5">
                    @csrf
                    <h3 class="text-sm font-semibold text-neutral">Reject</h3>
                    <p class="mt-1 text-xs text-neutral/60">Optional reason — for your records.</p>
                    <textarea name="rejection_reason" rows="3"
                        class="mt-3 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">{{ old('rejection_reason') }}</textarea>
                    <button type="submit"
                        class="mt-3 w-full rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                        onclick="return confirm('Reject this application?')">
                        Reject application
                    </button>
                </form>
            </div>
        @else
            <p class="mt-6 rounded-xl border border-neutral/10 bg-neutral/5 p-4 text-sm text-neutral/60">
                Already {{ $tenant->status }} on {{ $tenant->reviewed_at?->format('j M Y') }}.
            </p>
        @endif
    </div>
@endsection
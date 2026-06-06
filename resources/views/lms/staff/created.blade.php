@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <div class="rounded-xl border border-success/20 bg-success/5 p-6">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-success/15 text-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-semibold text-neutral">Staff member created</h1>
                <p class="mt-0.5 text-sm text-neutral/60">
                    {{ $staff->name }} ({{ ucfirst($staff->role) }}) can now sign in.
                </p>
            </div>
        </div>

        <div class="mt-6 rounded-lg border border-accent/30 bg-accent/5 p-4">
            <div class="text-xs font-semibold uppercase tracking-wide text-accent-dark">
                ⚠ Save these credentials now
            </div>
            <p class="mt-1 text-xs text-neutral/70">
                This page is shown only once. Share these with {{ $staff->name }} through a secure channel.
            </p>
            <dl class="mt-4 space-y-3 font-mono text-sm">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Email</dt>
                    <dd class="mt-1 select-all text-neutral">{{ $credentials['email'] }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Temporary password</dt>
                    <dd class="mt-1 select-all text-neutral">{{ $credentials['password'] }}</dd>
                </div>
            </dl>
        </div>

        <div class="mt-6">
            <a href="{{ route('lms.staff.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                Back to staff
            </a>
        </div>
    </div>
</div>
@endsection
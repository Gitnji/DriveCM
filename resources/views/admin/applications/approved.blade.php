@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-xl font-semibold text-neutral">Application approved</h1>
        <p class="mt-1 text-sm text-neutral/60">{{ $tenant->name }} is now active at <span class="font-mono">{{ $tenant->subdomain }}.drivecm.cm</span>.</p>

        <div class="mt-6 rounded-xl border border-accent/40 bg-accent/5 p-5">
            <div class="text-xs font-semibold uppercase tracking-wide text-accent">Login credentials — shown only once</div>
            <p class="mt-1 text-xs text-neutral/60">
                Communicate these to the school by phone/WhatsApp. If you refresh this page they will be gone — for security, DriveCM does not store the temporary password.
            </p>
            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2">
                    <dt class="text-neutral/50">Email</dt>
                    <dd class="font-mono">{{ $credentials['email'] }}</dd>
                </div>
                <div class="flex items-center justify-between rounded-lg bg-white px-3 py-2">
                    <dt class="text-neutral/50">Temporary password</dt>
                    <dd class="font-mono">{{ $credentials['password'] }}</dd>
                </div>
            </dl>
            <p class="mt-3 text-xs text-neutral/50">
                The owner will be required to set a new password on first login.
            </p>
        </div>

        <a href="{{ route('admin.applications.index') }}"
           class="mt-6 inline-block rounded-lg border border-neutral/20 px-4 py-2 text-sm font-semibold text-neutral hover:bg-surface">
            Back to applications
        </a>
    </div>
@endsection
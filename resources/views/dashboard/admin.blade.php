@extends('layouts.admin')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-xl font-semibold text-neutral">Platform overview</h1>
        <p class="mt-1 text-sm text-neutral/60">Super Admin — {{ $admin->name }}</p>

        <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
            <p class="text-sm text-neutral/70">
                School registration requests and tenant management will appear here as those features are added.
            </p>
        </div>
    </div>
@endsection
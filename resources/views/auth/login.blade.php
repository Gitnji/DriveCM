@extends('layouts.auth')

@section('content')
    <h1 class="text-lg font-semibold text-neutral">Sign in to your school</h1>
    <p class="mt-1 text-sm text-neutral/60">Enter your details to continue.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="tenant_id" class="block text-sm font-medium text-neutral">School</label>
            <select name="tenant_id" id="tenant_id" required
                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">Select your school</option>
                @foreach ($tenants as $tenant)
                    <option value="{{ $tenant->id }}" @selected(old('tenant_id') === $tenant->id)>
                        {{ $tenant->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-neutral">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-neutral">Password</label>
            <input type="password" name="password" id="password" required
                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
            Sign in
        </button>
    </form>
@endsection
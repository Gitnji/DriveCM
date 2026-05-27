@extends('layouts.auth')

@section('content')
    <h1 class="text-lg font-semibold text-neutral">Set a new password</h1>
    <p class="mt-1 text-sm text-neutral/60">You must change your password before continuing.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <label for="password" class="block text-sm font-medium text-neutral">New password</label>
            <input type="password" name="password" id="password" required autofocus
                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            <p class="mt-1 text-xs text-neutral/50">At least 8 characters.</p>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-neutral">Confirm new password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <button type="submit"
            class="w-full rounded-lg bg-success px-4 py-2.5 text-sm font-semibold text-white transition hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-success focus:ring-offset-2">
            Update password
        </button>
    </form>
@endsection
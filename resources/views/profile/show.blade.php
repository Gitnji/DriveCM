@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <h1 class="text-2xl font-bold text-neutral">Profile</h1>
    <p class="mt-1 text-sm text-neutral/60">Manage your account.</p>

    {{-- Profile form --}}
    <div class="mt-8 rounded-xl border border-neutral/10 bg-white p-6">
        <h2 class="text-lg font-semibold text-neutral">Account details</h2>

        @if (session('profile_status'))
            <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('profile_status') }}</div>
        @endif

        @if ($errors->default->any())
            <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->default->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-neutral" for="name">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral/50" for="email">Email <span class="text-neutral/40">(read-only)</span></label>
                <input type="email" id="email" value="{{ $user->email }}" readonly
                       class="mt-1 w-full rounded-lg border border-neutral/15 bg-surface px-3 py-2 text-sm text-neutral/60">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="phone">Phone <span class="text-neutral/40">(optional)</span></label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                       placeholder="+237 ...">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="town">Town <span class="text-neutral/40">(optional)</span></label>
                <input type="text" id="town" name="town" value="{{ old('town', $user->town) }}"
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="language">Language</label>
                <select id="language" name="language" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    <option value="en" @selected(old('language', $user->language) === 'en')>English</option>
                    <option value="fr" @selected(old('language', $user->language) === 'fr')>Français</option>
                </select>
            </div>

            <div class="border-t border-neutral/10 pt-5">
                <button type="submit"
                        class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    Save profile
                </button>
            </div>
        </form>
    </div>

    {{-- Password form --}}
    <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
        <h2 class="text-lg font-semibold text-neutral">Change password</h2>
        <p class="mt-1 text-sm text-neutral/60">Use a unique password not used elsewhere.</p>

        @if (session('password_status'))
            <div class="mt-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('password_status') }}</div>
        @endif

        @if ($errors->password->any())
            <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->password->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.password.update') }}" class="mt-5 space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-neutral" for="current_password">Current password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="password">New password</label>
                <input type="password" id="password" name="password" required minlength="8"
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <p class="mt-1 text-xs text-neutral/50">At least 8 characters.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="password_confirmation">Confirm new password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div class="border-t border-neutral/10 pt-5">
                <button type="submit"
                        class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    Change password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
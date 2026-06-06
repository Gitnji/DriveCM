@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('lms.staff.index') }}" class="text-sm font-medium text-primary hover:underline">← Staff</a>

    <h1 class="mt-3 text-2xl font-bold text-neutral">Add staff member</h1>
    <p class="mt-1 text-sm text-neutral/60">A temporary password will be shown once. Share it with the new staff member privately.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.staff.store') }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf

        <div>
            <label class="block text-sm font-medium text-neutral" for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            <p class="mt-1 text-xs text-neutral/50">They will sign in with this email.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="role">Role</label>
            <select id="role" name="role" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="">Select role</option>
                <option value="instructor" @selected(old('role') === 'instructor')>Instructor</option>
                <option value="secretary" @selected(old('role') === 'secretary')>Secretary</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="language">Preferred language</label>
            <select id="language" name="language" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="en" @selected(old('language', 'en') === 'en')>English</option>
                <option value="fr" @selected(old('language') === 'fr')>Français</option>
            </select>
        </div>

        <div class="flex gap-3 border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Create account
            </button>
            <a href="{{ route('lms.staff.index') }}"
               class="rounded-lg border border-neutral/20 px-5 py-2.5 text-sm font-semibold text-neutral hover:bg-surface">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
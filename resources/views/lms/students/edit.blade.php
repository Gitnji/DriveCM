@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-2xl">
    <a href="{{ route('lms.students.show', $student) }}" class="text-sm font-medium text-primary hover:underline">← {{ $student->name }}</a>

    <h1 class="mt-3 text-2xl font-bold text-neutral">Edit student</h1>
    <p class="mt-1 text-sm text-neutral/60">Email cannot be changed. To use a different email, remove this student and re-approve a new application.</p>

    @if ($errors->any())
        <div class="mt-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('lms.students.update', $student) }}" class="mt-6 space-y-5 rounded-xl border border-neutral/10 bg-white p-6">
        @csrf @method('PUT')

        <div>
            <label class="block text-sm font-medium text-neutral" for="name">Full name</label>
            <input type="text" id="name" name="name" value="{{ old('name', $student->name) }}" required
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral/50" for="email">Email <span class="text-neutral/40">(read-only)</span></label>
            <input type="email" id="email" value="{{ $student->email }}" readonly
                   class="mt-1 w-full rounded-lg border border-neutral/15 bg-surface px-3 py-2 text-sm text-neutral/60">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="phone">Phone</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $student->phone) }}"
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary"
                   placeholder="+237 ...">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="town">Town</label>
            <input type="text" id="town" name="town" value="{{ old('town', $student->town) }}" required
                   class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
        </div>

        <div>
            <label class="block text-sm font-medium text-neutral" for="language">Preferred language</label>
            <select id="language" name="language" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                <option value="en" @selected(old('language', $student->language) === 'en')>English</option>
                <option value="fr" @selected(old('language', $student->language) === 'fr')>Français</option>
            </select>
        </div>

        <div class="flex gap-3 border-t border-neutral/10 pt-5">
            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Save changes
            </button>
            <a href="{{ route('lms.students.show', $student) }}"
               class="rounded-lg border border-neutral/20 px-5 py-2.5 text-sm font-semibold text-neutral hover:bg-surface">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
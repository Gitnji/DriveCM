@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-neutral">Staff</h1>
            <p class="mt-1 text-sm text-neutral/60">Manage your school's secretaries and instructors.</p>
        </div>
        <a href="{{ route('lms.staff.create') }}"
           class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
            + Add staff
        </a>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Active staff --}}
    <div class="rounded-xl border border-neutral/10 bg-white">
        <div class="border-b border-neutral/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-neutral">Active</h2>
            <p class="mt-0.5 text-xs text-neutral/50">{{ $active->count() }} {{ \Illuminate\Support\Str::plural('member', $active->count()) }}.</p>
        </div>

        @if ($active->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-neutral/50">No staff yet. Click "+ Add staff" to start.</div>
        @else
            <div class="divide-y divide-neutral/10">
                @foreach ($active as $member)
                    @php
                        $roleBadge = $member->role === 'instructor'
                            ? ['bg-primary/10', 'text-primary-dark', 'Instructor']
                            : ['bg-success/10', 'text-success',      'Secretary'];
                    @endphp
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-sm font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($member->name) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral">{{ $member->name }}</div>
                                <div class="mt-0.5 text-xs text-neutral/50">{{ $member->email }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $roleBadge[0] }} {{ $roleBadge[1] }}">
                                {{ $roleBadge[2] }}
                            </span>
                            <a href="{{ route('lms.staff.edit', $member) }}" class="text-sm font-medium text-primary hover:underline">Edit</a>
                            <form method="POST" action="{{ route('lms.staff.destroy', $member) }}"
                                  onsubmit="return confirm('Remove {{ $member->name }} from staff? They will not be able to sign in.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Removed staff (soft-deleted) --}}
    @if ($deleted->isNotEmpty())
        <div class="mt-8 rounded-xl border border-neutral/10 bg-white">
            <div class="border-b border-neutral/10 px-6 py-4">
                <h2 class="text-lg font-semibold text-neutral">Removed</h2>
                <p class="mt-0.5 text-xs text-neutral/50">Soft-deleted — they cannot sign in. Restore to reactivate.</p>
            </div>
            <div class="divide-y divide-neutral/10">
                @foreach ($deleted as $member)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-neutral/20 text-sm font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($member->name) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral/50 line-through">{{ $member->name }}</div>
                                <div class="mt-0.5 text-xs text-neutral/40">
                                    {{ $member->email }} · Removed {{ $member->deleted_at?->diffForHumans() ?? 'unknown' }}
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('lms.staff.restore', $member->id) }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-primary hover:underline">Restore</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
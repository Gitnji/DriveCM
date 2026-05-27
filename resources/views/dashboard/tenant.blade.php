@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-xl font-semibold text-neutral">
            Welcome, {{ $user->name }}
        </h1>
        <p class="mt-1 text-sm text-neutral/60">
            Signed in as {{ ucfirst($user->role) }}.
        </p>

        <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
            <p class="text-sm text-neutral/70">
                @switch($user->role)
                    @case('owner')
                        Your school dashboard. Lessons, students, and reports will appear here as those features are added.
                        @break
                    @case('secretary')
                        Student registration and scheduling tools will appear here.
                        @break
                    @case('instructor')
                        Your lessons and practical schedule will appear here.
                        @break
                    @case('student')
                        Your theory lessons and progress will appear here.
                        @break
                @endswitch
            </p>
        </div>
    </div>
@endsection
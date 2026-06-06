@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-neutral">Students</h1>
        <p class="mt-1 text-sm text-neutral/60">All students enrolled at your school.</p>
    </div>

    @if (session('status'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">{{ session('status') }}</div>
    @endif

    {{-- Active --}}
    <div class="rounded-xl border border-neutral/10 bg-white">
        <div class="border-b border-neutral/10 px-6 py-4">
            <h2 class="text-lg font-semibold text-neutral">Active</h2>
            <p class="mt-0.5 text-xs text-neutral/50">{{ $active->count() }} {{ \Illuminate\Support\Str::plural('student', $active->count()) }}.</p>
        </div>

        @if ($active->isEmpty())
            <div class="px-6 py-12 text-center text-sm text-neutral/50">
                No students yet. Approve applications under <a href="{{ route('lms.enrollments.index') }}" class="text-primary hover:underline">Enrollments</a> to enroll students.
            </div>
        @else
            <div class="divide-y divide-neutral/10">
                @foreach ($active as $student)
                    <a href="{{ route('lms.students.show', $student) }}"
                       class="group flex items-center justify-between px-6 py-4 transition hover:bg-surface">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary to-primary-dark text-sm font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($student->name) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral group-hover:text-primary-dark">{{ $student->name }}</div>
                                <div class="mt-0.5 text-xs text-neutral/50">
                                    {{ $student->email }}
                                    @if ($student->phone)
                                        <span class="text-neutral/30">·</span> {{ $student->phone }}
                                    @endif
                                    @if ($student->town)
                                        <span class="text-neutral/30">·</span> {{ $student->town }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <span class="text-neutral/30 transition-transform group-hover:translate-x-0.5">→</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Removed --}}
    @if ($deleted->isNotEmpty())
        <div class="mt-8 rounded-xl border border-neutral/10 bg-white">
            <div class="border-b border-neutral/10 px-6 py-4">
                <h2 class="text-lg font-semibold text-neutral">Removed</h2>
                <p class="mt-0.5 text-xs text-neutral/50">Cannot sign in. Restore to reactivate.</p>
            </div>
            <div class="divide-y divide-neutral/10">
                @foreach ($deleted as $student)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-neutral/20 text-sm font-semibold text-white">
                                {{ \App\Support\InstructorAvatar::initials($student->name) }}
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-neutral/50 line-through">{{ $student->name }}</div>
                                <div class="mt-0.5 text-xs text-neutral/40">
                                    {{ $student->email }} · Removed {{ $student->deleted_at?->diffForHumans() ?? 'unknown' }}
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('lms.students.restore', $student->id) }}">
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
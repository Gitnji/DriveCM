@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-xl">
        <a href="{{ route('lms.practical.index') }}" class="text-sm font-medium text-primary hover:underline">← Practical sessions</a>
        <h1 class="mt-3 text-xl font-semibold text-neutral">Schedule a practical session</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('lms.practical.store') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-neutral">Student</label>
                <select name="student_id" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                    <option value="">Select student</option>
                    @foreach ($students as $s)
                        <option value="{{ $s->id }}" @selected((string) old('student_id') === (string) $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral">Instructor</label>
                <select name="instructor_id" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                    <option value="">Select instructor</option>
                    @foreach ($instructors as $i)
                        <option value="{{ $i->id }}" @selected((string) old('instructor_id') === (string) $i->id)>{{ $i->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-neutral">Date & time</label>
                    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at') }}" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral">Duration (minutes)</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 60) }}" min="15" max="480" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
            </div>

            {{-- D88 — override checkbox. Only an instructor's tick has effect; the controller
                 rejects a secretary or an unticked box when the theory gate isn't met. --}}
            <label class="flex items-start gap-2 rounded-lg bg-surface p-3 text-sm">
                <input type="checkbox" name="override_theory" value="1" class="mt-0.5" @checked(old('override_theory'))>
                <span class="text-neutral/70">
                    Override theory requirement — schedule even if the student hasn't completed Levels 1 &amp; 2.
                    <span class="text-neutral/40">(Instructors only; this is recorded.)</span>
                </span>
            </label>

            <button type="submit"
                class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Schedule session
            </button>
        </form>
    </div>
@endsection
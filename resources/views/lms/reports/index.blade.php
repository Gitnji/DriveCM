@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-xl font-semibold text-neutral">License Hours Reports</h1>
        <p class="mt-1 text-sm text-neutral/60">Theory and practical hours per student. Validate a report before exporting it for the Ministry.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <div class="mt-6 space-y-3">
            @forelse ($rows as $row)
                @php($student = $row['student'])
                @php($th = $row['theory_minutes'])
                @php($pr = $row['practical_minutes'])
                @php($validated = $row['is_current_validated'])
                @php($hasOldValidation = $row['latest_validation'] !== null)
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-sm font-medium text-neutral">{{ $student->name }}</div>
                            <div class="mt-1 text-xs text-neutral/50">
                                Theory: {{ intdiv($th, 60) }}h {{ $th % 60 }}m ·
                                Practical: {{ intdiv($pr, 60) }}h {{ $pr % 60 }}m
                            </div>

                            @if ($validated)
                                <div class="mt-1 text-xs font-medium text-success">
                                    ✓ Validated {{ $row['latest_validation']->created_at->format('j M Y') }}
                                </div>
                            @elseif ($hasOldValidation)
                                <div class="mt-1 text-xs font-medium text-accent">
                                    ⚠ Hours changed since last validation — re-validate before exporting.
                                </div>
                            @else
                                <div class="mt-1 text-xs text-neutral/40">Not yet validated.</div>
                            @endif
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            @can('validate-reports')
                                <form method="POST" action="{{ route('lms.reports.validate', $student) }}">
                                    @csrf
                                    <button type="submit"
                                        class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">
                                        {{ $validated ? 'Re-validate' : 'Validate' }}
                                    </button>
                                </form>
                            @endcan

                            {{-- Export — locked until validated (D91). R3 wires the actual PDF. --}}
                            @if ($validated)
                                <a href="{{ route('lms.reports.export', $student) }}"
                                   class="rounded-lg border border-neutral/20 px-3 py-1.5 text-sm font-semibold text-neutral hover:bg-surface">
                                    Export PDF
                                </a>
                            @else
                                <span class="text-xs text-neutral/30">Validate to export</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-8 text-center text-sm text-neutral/50">
                    No students yet.
                </p>
            @endforelse
        </div>
    </div>
@endsection
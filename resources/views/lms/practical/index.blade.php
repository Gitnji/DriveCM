@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex items-center justify-between">
            <h1 class="text-xl font-semibold text-neutral">Practical Sessions</h1>
            <a href="{{ route('lms.practical.create') }}"
               class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                Schedule session
            </a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        {{-- DASH-3 + DASH-4 filter indicator (D167) — shows when ?status=needs_attention is set --}}
        @if (! empty($filter) && $filter === 'needs_attention')
            <div class="mt-4 flex items-center justify-between rounded-lg border border-accent/30 bg-accent/5 px-3 py-2 text-sm">
                <div class="flex items-center gap-2 text-accent-dark">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-accent"></span>
                    <span class="font-semibold">Showing past sessions awaiting attendance.</span>
                    <span class="text-neutral/50">{{ $sessions->count() }} {{ \Illuminate\Support\Str::plural('session', $sessions->count()) }}</span>
                </div>
                <a href="{{ route('lms.practical.index') }}" class="text-xs font-semibold text-neutral/60 hover:text-neutral">
                    Clear filter ×
                </a>
            </div>
        @endif

        <div class="mt-6 space-y-3">
            @forelse ($sessions as $session)
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-medium text-neutral">
                                {{ $session->student->name }}
                                <span class="text-neutral/40">with</span>
                                {{ $session->instructor->name }}
                            </div>
                            <div class="mt-1 text-xs text-neutral/50">
                                {{ $session->scheduled_at->format('D j M Y, H:i') }} · {{ $session->duration_minutes }} min
                                @if ($session->theory_gate_overridden)
                                    · <span class="text-accent">theory override</span>
                                @endif
                            </div>
                        </div>
                        @php($badge = ['scheduled'=>'bg-primary/10 text-primary','completed'=>'bg-success/10 text-success','cancelled'=>'bg-neutral/10 text-neutral/50','absent'=>'bg-red-50 text-red-600'][$session->status])
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ ucfirst($session->status) }}</span>
                    </div>

                    {{-- Attendance marking — inline form (D82, D87 re-markable) --}}
                    <form method="POST" action="{{ route('lms.practical.mark', $session) }}"
                          class="mt-3 flex flex-wrap items-end gap-2 border-t border-neutral/10 pt-3">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs text-neutral/50">Mark as</label>
                            <select name="status" class="mt-1 rounded-lg border border-neutral/20 px-2 py-1.5 text-sm">
                                <option value="completed" @selected($session->status==='completed')>Completed</option>
                                <option value="cancelled" @selected($session->status==='cancelled')>Cancelled</option>
                                <option value="absent" @selected($session->status==='absent')>Absent</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-neutral/50">Actual minutes</label>
                            <input type="number" name="duration_minutes" value="{{ $session->duration_minutes }}" min="15" max="480"
                                class="mt-1 w-24 rounded-lg border border-neutral/20 px-2 py-1.5 text-sm">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs text-neutral/50">Notes</label>
                            <input type="text" name="notes" value="{{ $session->notes }}"
                                class="mt-1 w-full rounded-lg border border-neutral/20 px-2 py-1.5 text-sm">
                        </div>
                        <button type="submit"
                            class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">
                            Save
                        </button>
                    </form>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-8 text-center text-sm text-neutral/50">
                    No practical sessions yet.
                </p>
            @endforelse
        </div>
    </div>
@endsection
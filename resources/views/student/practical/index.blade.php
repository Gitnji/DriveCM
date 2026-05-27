@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-xl font-semibold text-neutral">My Practical Lessons</h1>

        {{-- Hours summary --}}
        <div class="mt-4 grid grid-cols-2 gap-3">
            <div class="rounded-xl border border-neutral/10 bg-white p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-neutral/40">Theory hours</div>
                <div class="mt-1 text-2xl font-bold text-primary">
                    {{ intdiv($theoryMinutes, 60) }}h {{ $theoryMinutes % 60 }}m
                </div>
            </div>
            <div class="rounded-xl border border-neutral/10 bg-white p-4">
                <div class="text-xs font-medium uppercase tracking-wide text-neutral/40">Practical hours</div>
                <div class="mt-1 text-2xl font-bold text-success">
                    {{ intdiv($practicalMinutes, 60) }}h {{ $practicalMinutes % 60 }}m
                </div>
            </div>
        </div>

        <h2 class="mt-8 text-sm font-semibold text-neutral">Sessions</h2>
        <div class="mt-2 space-y-2">
            @forelse ($sessions as $session)
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm font-medium text-neutral">
                                {{ $session->scheduled_at->format('D j M Y, H:i') }}
                            </div>
                            <div class="mt-1 text-xs text-neutral/50">
                                Instructor: {{ $session->instructor->name }} · {{ $session->duration_minutes }} min
                            </div>
                        </div>
                        @php($badge = ['scheduled'=>'bg-primary/10 text-primary','completed'=>'bg-success/10 text-success','cancelled'=>'bg-neutral/10 text-neutral/50','absent'=>'bg-red-50 text-red-600'][$session->status])
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $badge }}">{{ ucfirst($session->status) }}</span>
                    </div>
                    @if ($session->notes)
                        <p class="mt-2 border-t border-neutral/10 pt-2 text-xs text-neutral/60">{{ $session->notes }}</p>
                    @endif
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-8 text-center text-sm text-neutral/50">
                    No practical sessions scheduled yet.
                </p>
            @endforelse
        </div>
    </div>
@endsection
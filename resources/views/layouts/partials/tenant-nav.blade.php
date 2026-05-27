<nav class="px-3 py-4">
    <a href="{{ route('dashboard') }}"
       class="flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
        Dashboard
    </a>

    {{--
        Nav grows per feature (D30, Option A). As each batch lands, it adds its link here, e.g.:
        - Theory LMS batch:  Lessons  (owner + instructor: author; student: learn)
        - Students batch:    Students (owner, secretary)
        - Practical batch:   Schedule
        - Reports batch:     Reports
        Each wrapped in the relevant role helper, e.g. @if($u->canAuthorLessons()) ... @endif
    --}}

    @php($u = auth()->user())
    <p class="mt-6 px-3 text-xs uppercase tracking-wide text-white/40">
        {{ ucfirst($u->role) }}
    </p>
</nav>
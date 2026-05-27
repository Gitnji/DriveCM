<nav class="px-3 py-4">
    <a href="{{ route('dashboard') }}"
       class="flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
        Dashboard
    </a>

    @can('manage-levels')
        <a href="{{ route('lms.levels.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.levels.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Theory Levels
        </a>
    @endcan
    @can('author-lessons')
        <a href="{{ route('lms.lessons.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.lessons.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Lessons
        </a>
    @endcan
    @can('access-student-lessons')
        <a href="{{ route('student.lessons.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.lessons.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            My Lessons
        </a>
    @endcan

    @php($u = auth()->user())
    <p class="mt-6 px-3 text-xs uppercase tracking-wide text-white/40">
        {{ ucfirst($u->role) }}
    </p>
</nav>
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
    @can('manage-staff')
        <a href="{{ route('lms.staff.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.staff.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Staff
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
    @can('access-student-lessons')
        <a href="{{ route('student.practical.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('student.practical.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            My Practical
        </a>
    @endcan
    @can('schedule-practical')
        <a href="{{ route('lms.practical.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.practical.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Practical Sessions
        </a>
    @endcan
    @can('review-enrollments')
        <a href="{{ route('lms.enrollments.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.enrollments.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Enrollments
        </a>
    @endcan
    @can('preview-reports')
        <a href="{{ route('lms.reports.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.reports.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            License Reports
        </a>
    @endcan
    @can('manage-site')
        <a href="{{ route('site.pages.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('site.pages.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Your Website
        </a>
    @endcan
    @can('manage-site')
        <a href="{{ route('site.settings.edit') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('site.settings.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Appearance
        </a>
    @endcan
    

    @php($u = auth()->user())
    <p class="mt-6 px-3 text-xs uppercase tracking-wide text-white/40">
        {{ ucfirst($u->role) }}
    </p>
</nav>
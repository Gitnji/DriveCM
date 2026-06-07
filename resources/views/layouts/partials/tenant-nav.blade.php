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
    @can('manage-students')
        <a href="{{ route('lms.students.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.students.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            Students
        </a>
    @endcan
    @can('preview-reports')
        <a href="{{ route('lms.reports.index') }}"
           class="mt-1 flex items-center rounded-lg px-3 py-2 text-sm font-medium {{ request()->routeIs('lms.reports.*') ? 'bg-primary text-white' : 'text-white/70 hover:bg-primary/40' }}">
            License Reports
        </a>
    @endcan
    @can('manage-payments')
        @php
            $paymentsOpen = request()->routeIs('lms.payment-types.*') || request()->routeIs('lms.payment-settings.*');
        @endphp
        <div class="mt-1" data-nav-dropdown @if ($paymentsOpen) data-open @endif>
            <button type="button" data-nav-dropdown-toggle
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm font-medium {{ $paymentsOpen ? 'bg-primary/30 text-white' : 'text-white/70 hover:bg-primary/40' }}">
                <span>Payments</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round"
                     class="h-3.5 w-3.5 transition-transform" data-nav-dropdown-chevron>
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </button>
            <div data-nav-dropdown-panel class="{{ $paymentsOpen ? '' : 'hidden' }} ml-2 mt-1 border-l border-white/10 pl-2">
                <a href="{{ route('lms.payment-types.index') }}"
                   class="flex items-center rounded-lg px-3 py-2 text-sm {{ request()->routeIs('lms.payment-types.*') ? 'bg-primary text-white font-medium' : 'text-white/60 hover:bg-primary/40 hover:text-white' }}">
                    Types
                </a>
                <a href="{{ route('lms.payment-settings.edit') }}"
                   class="mt-0.5 flex items-center rounded-lg px-3 py-2 text-sm {{ request()->routeIs('lms.payment-settings.*') ? 'bg-primary text-white font-medium' : 'text-white/60 hover:bg-primary/40 hover:text-white' }}">
                    Receiving accounts
                </a>
            </div>
        </div>
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
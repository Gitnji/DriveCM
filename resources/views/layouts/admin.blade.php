<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#061B33">
    <title>{{ $title ?? 'DriveCM Admin' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-neutral antialiased">
    @php
        // Per-link helper: returns Tailwind classes based on whether the route is active.
        // Matches sub-routes too (e.g. admin.applications.show keeps "Applications" highlighted).
        $navLink = function (string $route) {
            $active = request()->routeIs($route) || request()->routeIs($route . '.*');
            return $active
                ? 'flex items-center gap-3 rounded-lg bg-white/15 px-3 py-2 text-sm font-semibold text-white'
                : 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/70 hover:bg-white/10 hover:text-white';
        };
    @endphp

    {{-- Mobile top bar (visible only below md) --}}
    <div class="flex h-14 items-center justify-between border-b border-neutral/10 bg-primary-dark px-4 text-white md:hidden">
        <span class="text-lg font-bold tracking-tight">
            Drive<span class="text-accent">CM</span>
            <span class="text-white/50 font-normal">Admin</span>
        </span>
        <button type="button" data-admin-sidebar-toggle
                class="rounded-lg p-2 text-white/80 hover:bg-white/10"
                aria-label="Open menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="h-6 w-6">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Sidebar + content layout --}}
    <div class="flex min-h-[calc(100vh-3.5rem)] md:min-h-screen">

        {{-- Sidebar: persistent on md+, slide-in drawer on mobile --}}
        <aside data-admin-sidebar
               class="fixed inset-y-0 left-0 z-40 hidden w-60 flex-col bg-primary-dark text-white md:flex md:static md:translate-x-0">
            {{-- Logo --}}
            <div class="flex h-16 items-center px-5">
                <a href="{{ route('admin.dashboard') }}" class="text-lg font-bold tracking-tight">
                    Drive<span class="text-accent">CM</span>
                    <span class="text-white/50 font-normal">Admin</span>
                </a>
            </div>

            {{-- Nav links --}}
            <nav class="flex-1 space-y-1 px-3 py-2">
                <a href="{{ route('admin.dashboard') }}" class="{{ $navLink('admin.dashboard') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <rect x="3" y="3" width="7" height="9"/>
                        <rect x="14" y="3" width="7" height="5"/>
                        <rect x="14" y="12" width="7" height="9"/>
                        <rect x="3" y="16" width="7" height="5"/>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('admin.applications.index') }}" class="{{ $navLink('admin.applications.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                    Applications
                </a>
                <a href="{{ route('admin.platform-settings.edit') }}" class="{{ $navLink('admin.platform-settings.edit') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    Platform Settings
                </a>
            </nav>

            {{-- User info + sign out (at bottom) --}}
            <div class="border-t border-white/10 px-3 py-3">
                <div class="px-2 pb-2 text-xs text-white/50">Signed in as</div>
                <div class="px-2 pb-3 text-sm font-medium text-white">{{ auth('admin')->user()->name }}</div>
                <form method="POST" action="{{ route('admin.login.destroy') }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-white/70 hover:bg-white/10 hover:text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                        Sign out
                    </button>
                </form>
            </div>
        </aside>

        {{-- Mobile drawer backdrop --}}
        <div data-admin-sidebar-backdrop
             class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

        {{-- Main content --}}
        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    {{-- Mobile sidebar toggle. Vanilla JS, no library. --}}
    <script>
        (function () {
            const sidebar = document.querySelector('[data-admin-sidebar]');
            const backdrop = document.querySelector('[data-admin-sidebar-backdrop]');
            const toggle = document.querySelector('[data-admin-sidebar-toggle]');

            if (! sidebar || ! backdrop || ! toggle) return;

            function open() {
                sidebar.classList.remove('hidden');
                sidebar.classList.add('flex');
                backdrop.classList.remove('hidden');
            }
            function close() {
                // Only close on mobile — md:flex keeps it open on desktop regardless of these classes
                if (window.matchMedia('(min-width: 768px)').matches) return;
                sidebar.classList.add('hidden');
                sidebar.classList.remove('flex');
                backdrop.classList.add('hidden');
            }

            toggle.addEventListener('click', () => {
                if (sidebar.classList.contains('hidden')) open();
                else close();
            });
            backdrop.addEventListener('click', close);
        })();
    </script>
</body>
</html>
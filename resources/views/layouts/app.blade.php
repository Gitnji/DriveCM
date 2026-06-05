<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0A3D62">
    <title>{{ $title ?? 'DriveCM' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface text-neutral antialiased">

    {{-- Mobile top bar (visible only below lg) --}}
    <div class="flex h-14 items-center justify-between border-b border-neutral/10 bg-primary-dark px-4 text-white lg:hidden">
        <span class="text-lg font-bold tracking-tight">
            Drive<span class="text-accent">CM</span>
        </span>
        <button id="sidebar-toggle" type="button" aria-label="Open menu"
                class="rounded-lg p-2 text-white/80 hover:bg-white/10">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <line x1="3" y1="6" x2="21" y2="6"/>
                <line x1="3" y1="12" x2="21" y2="12"/>
                <line x1="3" y1="18" x2="21" y2="18"/>
            </svg>
        </button>
    </div>

    <div class="flex min-h-[calc(100vh-3.5rem)] lg:min-h-screen">

        {{-- Sidebar: persistent on lg+, drawer on mobile --}}
        <aside id="sidebar"
               class="fixed inset-y-0 left-0 z-30 flex w-64 -translate-x-full flex-col bg-primary-dark text-white transition-transform lg:static lg:translate-x-0">
            {{-- Logo --}}
            <div class="flex h-16 items-center px-6">
                <a href="{{ route('dashboard') }}" class="text-xl font-bold tracking-tight">
                    Drive<span class="text-accent">CM</span>
                </a>
            </div>

            {{-- Nav (role-filtered) --}}
            <div class="flex-1 overflow-y-auto">
                @include('layouts.partials.tenant-nav')
            </div>

            {{-- User info + sign out at bottom --}}
            <div class="border-t border-white/10 px-3 py-3">
                <div class="px-2 pb-2 text-xs text-white/50">Signed in as</div>
                <div class="px-2 pb-3 text-sm font-medium text-white">{{ auth()->user()->name }}</div>
                <form method="POST" action="{{ route('login.destroy') }}">
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

        {{-- Mobile backdrop --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-20 hidden bg-black/40 lg:hidden"></div>

        {{-- Main content --}}
        <main class="flex-1 p-4 lg:p-8">
            @yield('content')
        </main>
    </div>

    <script>
        (function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggle = document.getElementById('sidebar-toggle');
            function open() {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }
            function close() {
                if (window.matchMedia('(min-width: 1024px)').matches) return;
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
            toggle && toggle.addEventListener('click', open);
            backdrop && backdrop.addEventListener('click', close);
        })();
    </script>
</body>
</html>
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
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-30 w-64 -translate-x-full bg-primary-dark text-white transition-transform lg:static lg:translate-x-0">
            <div class="flex h-16 items-center px-6">
                <span class="text-xl font-bold tracking-tight">Drive<span class="text-accent">CM</span></span>
            </div>
            @include('layouts.partials.tenant-nav')
        </aside>

        {{-- Backdrop for mobile --}}
        <div id="sidebar-backdrop" class="fixed inset-0 z-20 hidden bg-black/40 lg:hidden"></div>

        {{-- Main column --}}
        <div class="flex flex-1 flex-col">
            <header class="flex h-16 items-center justify-between border-b border-neutral/10 bg-white px-4 lg:px-6">
                <button id="sidebar-toggle" class="text-neutral lg:hidden" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
                <div class="flex items-center gap-3 text-sm">
                    <span class="text-neutral/70">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('login.destroy') }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-3 py-1.5 text-sm font-medium text-primary hover:bg-surface">
                            Sign out
                        </button>
                    </form>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle — vanilla JS, no Alpine (blueprint §2.1)
        (function () {
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const toggle = document.getElementById('sidebar-toggle');
            function open() { sidebar.classList.remove('-translate-x-full'); backdrop.classList.remove('hidden'); }
            function close() { sidebar.classList.add('-translate-x-full'); backdrop.classList.add('hidden'); }
            toggle && toggle.addEventListener('click', open);
            backdrop && backdrop.addEventListener('click', close);
        })();
    </script>
</body>
</html>
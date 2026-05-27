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
    <header class="flex h-16 items-center justify-between border-b border-neutral/10 bg-primary-dark px-4 text-white lg:px-6">
        <span class="text-lg font-bold tracking-tight">Drive<span class="text-accent">CM</span> <span class="text-white/50 font-normal">Admin</span></span>
        <div class="flex items-center gap-3 text-sm">
            <span class="text-white/70">{{ $admin->name }}</span>
            <form method="POST" action="{{ route('admin.login.destroy') }}">
                @csrf
                <button type="submit" class="rounded-lg px-3 py-1.5 text-sm font-medium text-white hover:bg-white/10">
                    Sign out
                </button>
            </form>
        </div>
    </header>
    <main class="p-4 lg:p-8">
        @yield('content')
    </main>
</body>
</html>
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
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-10">

        <div class="mb-8 text-center">
            <span class="text-2xl font-bold tracking-tight text-primary">Drive<span class="text-accent">CM</span></span>
            <p class="mt-1 text-sm text-neutral/60">Driving school management</p>
        </div>

        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-sm ring-1 ring-neutral/10 sm:p-8">
            @yield('content')
        </div>

        <p class="mt-8 text-xs text-neutral/40">&copy; {{ date('Y') }} DriveCM</p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DriveCM — coming soon</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-surface text-neutral">
    <div class="mx-auto flex min-h-screen max-w-lg flex-col items-center justify-center px-6 text-center">
        <h1 class="text-3xl font-bold">Drive<span class="text-accent">CM</span></h1>
        <p class="mt-3 text-sm text-neutral/60">
            Driving school management for Cameroon. Public site coming soon.
        </p>
        <a href="{{ route('apply.create') }}"
           class="mt-6 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
            Apply your driving school
        </a>
    </div>
</body>
</html>
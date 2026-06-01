<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $page->meta_title ?? $page->title ?? 'School')</title>
    @if (! empty($page?->meta_description))
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @vite(['resources/css/app.css'])
</head>
<body class="bg-surface text-neutral antialiased">
    @yield('content')
</body>
</html>
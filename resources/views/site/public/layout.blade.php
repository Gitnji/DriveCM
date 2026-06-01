<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $page->meta_title ?? $page->title ?? $tenant->name ?? 'School')</title>
    @if (! empty($page?->meta_description))
        <meta name="description" content="{{ $page->meta_description }}">
    @endif
    @vite(['resources/css/app.css'])
    <style>:root { --color-primary: {{ $siteSettings['primary_color'] }}; }</style>
</head>
<body class="bg-surface text-neutral antialiased">
    {{-- HEADER --}}
    <header class="bg-primary text-white">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-6 py-4">
            <a href="{{ route('tenant.public.home') }}" class="flex items-center gap-2">
                @if (! empty($siteSettings['logo_url']))
                    <img src="{{ $siteSettings['logo_url'] }}" alt="{{ $tenant->name }}"
                         class="h-8 rounded bg-white/95 p-1">
                @else
                    <span class="font-bold tracking-tight">{{ $tenant->name }}</span>
                @endif
            </a>
            @if ($navPages->isNotEmpty())
                <nav class="flex items-center gap-1 text-sm">
                    @foreach ($navPages as $nav)
                        <a href="{{ $nav->is_home ? route('tenant.public.home') : route('tenant.public.show', $nav->slug) }}"
                           class="rounded-lg px-3 py-1.5 hover:bg-white/10 {{ ($page && $page->id === $nav->id) ? 'bg-white/15 font-semibold' : '' }}">
                            {{ $nav->title }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </header>

    {{-- CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="mt-12 border-t border-neutral/10 bg-white">
        <div class="mx-auto max-w-5xl px-6 py-8 text-sm text-neutral/70">
            <div class="font-semibold text-neutral">{{ $tenant->name }}</div>
            <div class="mt-1 text-xs">© {{ now()->year }} {{ $tenant->name }}. All rights reserved.</div>

            @if ($siteSettings['footer_show_email'] || $siteSettings['footer_show_phone'])
                <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs">
                    @if ($siteSettings['footer_show_email'] && $tenant->contact_email)
                        <span>{{ $tenant->contact_email }}</span>
                    @endif
                    @if ($siteSettings['footer_show_phone'] && $tenant->contact_phone)
                        <span>{{ $tenant->contact_phone }}</span>
                    @endif
                </div>
            @endif

            <div class="mt-4 text-xs text-neutral/40">
                Powered by
                <a href="https://drivecm.cm" class="font-medium text-blue-600 hover:underline">DriveCM</a>
            </div>
        </div>
    </footer>
</body>
</html>
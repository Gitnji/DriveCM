{{-- CMS-5f (D152) — contact + map. Two-column desktop, stacked mobile.
     Map iframe src constructed server-side from urlencoded address; host hardcoded; sandboxed. --}}
@php
    $heading = $block['heading'] ?? '';
    $address = $block['address'] ?? '';
    $phone = $block['phone'] ?? '';
    $email = $block['email'] ?? '';
    $hours = $block['hours'] ?? '';

    // D152.4 — host is hardcoded; only the address (urlencoded) is dynamic.
    $mapSrc = $address !== ''
        ? 'https://www.google.com/maps?q=' . urlencode($address) . '&output=embed'
        : null;
@endphp

<section class="px-6 py-12">
    <div class="mx-auto max-w-5xl">
        @if ($heading !== '')
            <h2 class="mb-6 text-2xl font-bold text-neutral">{{ $heading }}</h2>
        @endif

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            {{-- Contact info column --}}
            <div class="space-y-4 text-sm text-neutral/80">
                @if ($address !== '')
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Address</div>
                        <div class="mt-1 whitespace-pre-line">{{ $address }}</div>
                    </div>
                @endif
                @if ($phone !== '')
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Phone</div>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $phone) }}" class="mt-1 block text-primary hover:underline">
                            {{ $phone }}
                        </a>
                    </div>
                @endif
                @if ($email !== '')
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Email</div>
                        <a href="mailto:{{ $email }}" class="mt-1 block text-primary hover:underline">{{ $email }}</a>
                    </div>
                @endif
                @if ($hours !== '')
                    <div>
                        <div class="text-xs font-semibold uppercase tracking-wide text-neutral/50">Hours</div>
                        <div class="mt-1 whitespace-pre-line">{{ $hours }}</div>
                    </div>
                @endif
            </div>

            {{-- Map column --}}
            <div>
                @if ($mapSrc)
                    <iframe
                        src="{{ $mapSrc }}"
                        class="aspect-video w-full rounded-xl border border-neutral/10"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        sandbox="allow-scripts allow-same-origin allow-popups"
                        title="Map"></iframe>
                @else
                    <div class="flex aspect-video w-full items-center justify-center rounded-xl border border-dashed border-neutral/20 text-sm text-neutral/40">
                        Map appears here when an address is set.
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
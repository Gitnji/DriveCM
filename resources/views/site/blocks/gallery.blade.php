{{-- CMS-5b (D147) — gallery: responsive grid, no JS. --}}
@php($items = $block['children'] ?? [])

@if (count($items) > 0)
    <section class="px-6 py-12">
        <div class="mx-auto max-w-5xl">
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $img)
                    @if (! empty($img['url']))
                        <img src="{{ $img['url'] }}" alt="{{ $img['alt'] ?? '' }}"
                             class="aspect-[4/3] w-full rounded-lg border border-neutral/10 object-cover">
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
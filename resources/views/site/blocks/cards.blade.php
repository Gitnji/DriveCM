{{-- CMS-5c (D149) — cards: responsive 3/2/1 grid with icon + heading + text per card. --}}
@php
    $items = $block['children'] ?? [];
    $iconsClass = \App\Support\PageIcons::class;
@endphp

@if (count($items) > 0)
    <section class="px-6 py-12">
        <div class="mx-auto max-w-5xl">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($items as $card)
                    <div class="rounded-xl border border-neutral/10 bg-white p-6">
                        @php($iconSvg = ! empty($card['icon']) ? $iconsClass::get($card['icon']) : null)
                        @if ($iconSvg)
                            <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                {!! $iconSvg !!}
                            </div>
                        @endif
                        @if (! empty($card['heading']))
                            <h3 class="text-lg font-semibold text-neutral">{{ $card['heading'] }}</h3>
                        @endif
                        @if (! empty($card['text']))
                            <p class="mt-2 text-sm text-neutral/70">{{ $card['text'] }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
{{-- CMS-5d (D150) — pricing tiers: responsive 3/2/1 grid, each tier with icon, title, price,
     period, features (children), and optional CTA. --}}
@php
    $tiers = $block['children'] ?? [];
    $iconsClass = \App\Support\PageIcons::class;
@endphp

@if (count($tiers) > 0)
    <section class="px-6 py-12">
        <div class="mx-auto max-w-5xl">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($tiers as $tier)
                    <div class="flex flex-col rounded-xl border border-neutral/10 bg-white p-6">
                        @php($iconSvg = ! empty($tier['icon']) ? $iconsClass::get($tier['icon']) : null)
                        @if ($iconSvg)
                            <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary">
                                {!! $iconSvg !!}
                            </div>
                        @endif

                        @if (! empty($tier['title']))
                            <h3 class="text-lg font-semibold text-neutral">{{ $tier['title'] }}</h3>
                        @endif

                        @if (! empty($tier['price']))
                            <div class="mt-2 flex items-baseline gap-1">
                                <span class="text-2xl font-bold text-neutral">{{ $tier['price'] }}</span>
                                @if (! empty($tier['period']))
                                    <span class="text-sm text-neutral/60">{{ $tier['period'] }}</span>
                                @endif
                            </div>
                        @endif

                        @php($features = $tier['children'] ?? [])
                        @if (count($features) > 0)
                            <ul class="mt-4 space-y-2 text-sm text-neutral/70">
                                @foreach ($features as $feature)
                                    @if (! empty($feature['text']))
                                        <li class="flex items-start gap-2">
                                            <span class="mt-0.5 inline-flex h-4 w-4 shrink-0 items-center justify-center text-primary">
                                                {!! $iconsClass::get('check') !!}
                                            </span>
                                            <span>{{ $feature['text'] }}</span>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @endif

                        @if (! empty($tier['cta_text']) && ! empty($tier['cta_url']))
                            <a href="{{ $tier['cta_url'] }}"
                               class="mt-6 inline-block rounded-lg bg-primary px-5 py-2 text-center text-sm font-semibold text-white hover:bg-primary-dark">
                                {{ $tier['cta_text'] }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
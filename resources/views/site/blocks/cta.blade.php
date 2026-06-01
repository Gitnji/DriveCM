{{-- CMS-5a (D144) — CTA block. Uses tenant primary color unless background_color is set. --}}
@php
    $bg = ! empty($block['background_color']) ? $block['background_color'] : null;
@endphp

<section
    @if ($bg) style="background-color: {{ $bg }};" @endif
    class="{{ $bg ? 'text-white' : 'bg-primary text-white' }} px-6 py-16">
    <div class="mx-auto max-w-3xl text-center">
        @if (! empty($block['heading']))
            <h2 class="text-2xl font-bold sm:text-3xl">{{ $block['heading'] }}</h2>
        @endif
        @if (! empty($block['subtext']))
            <p class="mt-3 text-base opacity-90">{{ $block['subtext'] }}</p>
        @endif
        @if (! empty($block['button_text']) && ! empty($block['button_url']))
            <a href="{{ $block['button_url'] }}"
               class="mt-6 inline-block rounded-lg bg-white/95 px-6 py-3 text-sm font-semibold text-neutral hover:bg-white">
                {{ $block['button_text'] }}
            </a>
        @endif
    </div>
</section>
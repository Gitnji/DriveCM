<section
    @if (! empty($block['background_url']))
        style="background-image: url('{{ $block['background_url'] }}'); background-size: cover; background-position: center;"
    @endif
    class="relative {{ ! empty($block['background_url']) ? 'bg-primary-dark/40 text-white' : 'bg-primary-dark text-white' }} px-6 py-20 lg:py-32">
    <div class="relative mx-auto max-w-3xl text-center {{ ! empty($block['background_url']) ? 'drop-shadow' : '' }}">
        @if (! empty($block['heading']))
            <h1 class="text-3xl font-bold sm:text-5xl">{{ $block['heading'] }}</h1>
        @endif
        @if (! empty($block['subtext']))
            <p class="mt-4 text-base sm:text-lg opacity-90">{{ $block['subtext'] }}</p>
        @endif
        @if (! empty($block['cta_text']) && ! empty($block['cta_url']))
            <a href="{{ $block['cta_url'] }}"
               class="mt-6 inline-block rounded-lg bg-accent px-6 py-3 text-sm font-semibold text-primary-dark hover:opacity-90">
                {{ $block['cta_text'] }}
            </a>
        @endif
    </div>
</section>
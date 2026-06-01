<section class="px-6 py-8">
    <figure class="mx-auto max-w-2xl">
        @if (! empty($block['url']))
            <img src="{{ $block['url'] }}" alt="{{ $block['alt'] ?? '' }}"
                 class="w-full rounded-xl border border-neutral/10">
        @endif
        @if (! empty($block['caption']))
            <figcaption class="mt-2 text-center text-xs text-neutral/50">{{ $block['caption'] }}</figcaption>
        @endif
    </figure>
</section>
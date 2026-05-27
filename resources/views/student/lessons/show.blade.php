@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('student.lessons.index') }}" class="text-sm font-medium text-primary hover:underline">← My Lessons</a>

        <h1 class="mt-3 text-xl font-semibold text-neutral">{{ $lesson->title }}</h1>

        <div class="mt-6 space-y-6">
            @forelse ($lesson->content ?? [] as $block)
                @switch($block['type'] ?? '')
                    @case('text')
                        <div class="prose prose-sm max-w-none text-neutral">
                            {!! $block['html'] ?? '' !!}
                        </div>
                        @break

                    @case('image')
                        <figure>
                            <img src="{{ $block['url'] ?? '' }}" alt="{{ $block['alt'] ?? '' }}"
                                 class="w-full rounded-xl border border-neutral/10">
                            @if (!empty($block['alt']))
                                <figcaption class="mt-1 text-center text-xs text-neutral/50">{{ $block['alt'] }}</figcaption>
                            @endif
                        </figure>
                        @break

                    @case('video')
                        @php($embed = \App\Support\VideoEmbed::toEmbedUrl($block['embed_url'] ?? ''))
                        @if ($embed)
                            <div class="aspect-video overflow-hidden rounded-xl border border-neutral/10">
                                <iframe src="{{ $embed }}" class="h-full w-full" frameborder="0"
                                        allowfullscreen loading="lazy"></iframe>
                            </div>
                        @else
                            <a href="{{ $block['embed_url'] ?? '#' }}" target="_blank" rel="noopener"
                               class="text-sm font-medium text-primary hover:underline">
                                Watch video ↗
                            </a>
                        @endif
                        @break
                @endswitch
            @empty
                <p class="text-sm text-neutral/50">This lesson has no content yet.</p>
            @endforelse
        </div>

        <div class="mt-10 border-t border-neutral/10 pt-6">
            @if ($lesson->questions()->exists())
                <a href="{{ route('student.test.show', $lesson) }}"
                   class="inline-block rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    Take the test
                </a>
            @else
                {{-- D80 — test-less lesson: explicit finish. --}}
                <form method="POST" action="{{ route('student.test.finish', $lesson) }}">
                    @csrf
                    <button type="submit"
                        class="rounded-lg bg-success px-5 py-2.5 text-sm font-semibold text-white hover:opacity-90">
                        Finish lesson
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
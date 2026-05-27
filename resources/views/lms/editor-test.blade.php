@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <h1 class="text-xl font-semibold text-neutral">Block editor — test harness</h1>
        <p class="mt-1 text-sm text-neutral/60">A2-c: all three block types — text, image, video.</p>

        <div data-block-editor data-initial-blocks="[]"
             data-upload-url="{{ route('lms.uploads.store') }}"
             data-csrf="{{ csrf_token() }}"
             class="mt-6">
            <div class="mb-3 flex gap-2">
                <button type="button" data-add-block="text"
                    class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    + Text block
                </button>
                <button type="button" data-add-block="image"
                    class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    + Image block
                </button>
                <button type="button" data-add-block="video"
                    class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    + Video block
                </button>
            </div>

            <div data-block-list class="space-y-2"></div>

            <textarea data-block-output rows="4" readonly
                class="mt-4 w-full rounded-lg bg-surface p-3 font-mono text-xs text-neutral/60"></textarea>
            <p class="mt-1 text-xs text-neutral/40">↑ Live serialized output (this becomes a hidden field in A2-d).</p>
        </div>
    </div>
@endsection
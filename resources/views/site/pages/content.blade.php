@extends('layouts.app')

@push('head')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">
@endpush

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('site.pages.index') }}" class="text-sm font-medium text-primary hover:underline">← Your Website</a>
        <div class="mt-3 flex items-center justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold text-neutral">{{ $page->title }}</h1>
                <p class="mt-1 text-xs text-neutral/50 font-mono">/{{ $page->slug }}</p>
            </div>
            <a href="{{ route('site.pages.edit', $page) }}" class="text-sm font-medium text-primary hover:underline">Settings</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('site.pages.update-content', $page) }}" class="mt-6">
            @csrf @method('PUT')

            <div data-page-editor
                 data-upload-url="{{ route('lms.uploads.store') }}"
                 data-csrf="{{ csrf_token() }}"
                 data-initial-blocks="{{ old('content') ?? json_encode($page->content ?? []) }}"
                 class="mt-1">

                <div class="mb-3 flex flex-wrap gap-2">
                    <button type="button" data-add-block="hero"
                        class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">+ Hero</button>
                    <button type="button" data-add-block="rich_text"
                        class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">+ Text</button>
                    <button type="button" data-add-block="image"
                        class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">+ Image</button>
                </div>

                <div data-block-list class="space-y-2"></div>

                {{-- The editor writes JSON here; this hidden field is what posts (D51 amended, D133). --}}
                <textarea data-block-output name="content" class="hidden"></textarea>
            </div>

            <button type="submit"
                class="mt-6 rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Save content
            </button>
        </form>
    </div>
@endsection
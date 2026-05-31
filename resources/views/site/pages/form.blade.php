@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-xl">
        @php($isEdit = $page->exists)
        <a href="{{ route('site.pages.index') }}" class="text-sm font-medium text-primary hover:underline">← Your Website</a>
        <h1 class="mt-3 text-xl font-semibold text-neutral">{{ $isEdit ? 'Page settings' : 'New page' }}</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $isEdit ? route('site.pages.update', $page) : route('site.pages.store') }}"
              class="mt-6 space-y-4">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-neutral">Title</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral">URL slug</label>
                <div class="mt-1 flex items-stretch overflow-hidden rounded-lg border border-neutral/20">
                    <span class="border-r border-neutral/20 bg-surface px-3 py-2 text-sm text-neutral/50">/</span>
                    <input type="text" name="slug" value="{{ old('slug', $page->slug) }}" required placeholder="about"
                        class="flex-1 px-3 py-2 text-sm font-mono">
                </div>
                <p class="mt-1 text-xs text-neutral/50">Lowercase letters, numbers and hyphens. For the home page, use "home" and tick "Set as home" below.</p>
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_home" value="1" id="is_home" @checked(old('is_home', $page->is_home))>
                <label for="is_home" class="text-sm text-neutral">Set as home page (shown at your site root)</label>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral">Status</label>
                <select name="status" class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                    <option value="draft" @selected(old('status', $page->status) === 'draft')>Draft (not public)</option>
                    <option value="published" @selected(old('status', $page->status) === 'published')>Published (live)</option>
                </select>
            </div>

            <details class="rounded-lg border border-neutral/10 p-3">
                <summary class="cursor-pointer text-sm font-medium text-neutral/70">SEO (optional)</summary>
                <div class="mt-3 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-neutral">Meta title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
                            class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-neutral">Meta description</label>
                        <textarea name="meta_description" rows="2"
                            class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">{{ old('meta_description', $page->meta_description) }}</textarea>
                    </div>
                </div>
            </details>

            <button type="submit"
                class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                {{ $isEdit ? 'Save settings' : 'Create page' }}
            </button>
        </form>
    </div>
@endsection
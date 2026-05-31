@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-neutral">Your Website</h1>
                <p class="mt-1 text-sm text-neutral/60">Pages shown on your school's public site.</p>
            </div>
            <a href="{{ route('site.pages.create') }}"
               class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                New page
            </a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        <div class="mt-6 space-y-2">
            @forelse ($pages as $page)
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-neutral">{{ $page->title }}</span>
                                @if ($page->is_home)
                                    <span class="rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary">Home</span>
                                @endif
                                @if ($page->status === 'published')
                                    <span class="rounded-full bg-success/10 px-2 py-0.5 text-xs font-medium text-success">Published</span>
                                @else
                                    <span class="rounded-full bg-neutral/10 px-2 py-0.5 text-xs font-medium text-neutral/50">Draft</span>
                                @endif
                            </div>
                            <div class="mt-1 text-xs text-neutral/50 font-mono">/{{ $page->slug }}</div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2">
                            {{-- "Edit content" (the block editor) comes in CMS-2b --}}
                            <a href="{{ route('site.pages.edit-content', $page) }}" class="text-sm font-medium text-primary hover:underline">Edit content</a>
                            <a href="{{ route('site.pages.edit', $page) }}" class="text-sm font-medium text-primary hover:underline">Settings</a>
                            <form method="POST" action="{{ route('site.pages.destroy', $page) }}"
                                  onsubmit="return confirm('Delete this page?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-8 text-center text-sm text-neutral/50">
                    No pages yet. Create your first page to start building your site.
                </p>
            @endforelse
        </div>
    </div>
@endsection
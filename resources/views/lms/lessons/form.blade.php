@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        @php($isEdit = $lesson->exists)
        <h1 class="text-xl font-semibold text-neutral">{{ $isEdit ? 'Edit lesson' : 'New lesson' }}</h1>

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST"
              action="{{ $isEdit ? route('lms.lessons.update', $lesson) : route('lms.lessons.store') }}"
              class="mt-6 space-y-4">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-neutral">Title</label>
                <input type="text" name="title" value="{{ old('title', $lesson->title) }}" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral">Level</label>
                <select name="level_id" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" @selected((string) old('level_id', $lesson->level_id) === (string) $level->id)>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-neutral">Position</label>
                    <input type="number" name="position" value="{{ old('position', $lesson->position ?? 1) }}" min="1" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral">Pass %</label>
                    <input type="number" name="pass_threshold" value="{{ old('pass_threshold', $lesson->pass_threshold ?? 80) }}" min="1" max="100" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-neutral">Minutes</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $lesson->duration_minutes ?? 0) }}" min="0" required
                        class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral">Status</label>
                <select name="status" required
                    class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                    <option value="draft" @selected(old('status', $lesson->status) === 'draft')>Draft</option>
                    <option value="published" @selected(old('status', $lesson->status) === 'published')>Published</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral">Lesson content</label>
                <div data-block-editor
                     data-upload-url="{{ route('lms.uploads.store') }}"
                     data-csrf="{{ csrf_token() }}"
                     data-initial-blocks="{{ old('content') ?? ($isEdit ? json_encode($lesson->content ?? []) : '[]') }}"
                     class="mt-1">
                    <div class="mb-3 flex gap-2">
                        <button type="button" data-add-block="text"
                            class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">+ Text</button>
                        <button type="button" data-add-block="image"
                            class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">+ Image</button>
                        <button type="button" data-add-block="video"
                            class="rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-primary-dark">+ Video</button>
                    </div>
                    <div data-block-list class="space-y-2"></div>
                    {{-- The editor writes JSON here; this hidden field is what posts (D51 amended). --}}
                    <textarea data-block-output name="content" class="hidden"></textarea>
                </div>
            </div>

            <button type="submit"
                class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                {{ $isEdit ? 'Save changes' : 'Create lesson' }}
            </button>
        </form>
    </div>
@endsection
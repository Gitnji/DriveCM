@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-neutral">Questions</h1>
                <p class="mt-1 text-sm text-neutral/60">{{ $lesson->title }}</p>
            </div>
            <a href="{{ route('lms.lessons.index') }}" class="text-sm font-medium text-primary hover:underline">← Lessons</a>
        </div>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

       {{-- Existing questions --}}
        <div class="mt-6 space-y-2">
            @forelse ($questions as $i => $q)
                @php
                    $editPayload = [
                        'type' => $q->type,
                        'prompt' => $q->prompt,
                        'options' => $q->options->map->only('text', 'is_correct')->all(),
                        'image_upload_id' => $q->image_upload_id,
                        'image_url' => $q->image ? route('lms.uploads.show', $q->image) : null,
                    ];
                    // L5 — first/last detection for reorder arrow disable state.
                    $isFirst = $i === 0;
                    $isLast  = $i === $questions->count() - 1;
                @endphp
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            {{-- L5 — reorder arrows --}}
                            <div class="flex flex-col gap-0.5 pt-1">
                                <form method="POST" action="{{ route('lms.questions.reorder', [$lesson, $q, 'up']) }}">
                                    @csrf
                                    <button type="submit" @disabled($isFirst) title="Move up"
                                            class="rounded p-1 text-neutral/40 hover:text-primary disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:text-neutral/40">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                            <polyline points="18 15 12 9 6 15"/>
                                        </svg>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('lms.questions.reorder', [$lesson, $q, 'down']) }}">
                                    @csrf
                                    <button type="submit" @disabled($isLast) title="Move down"
                                            class="rounded p-1 text-neutral/40 hover:text-primary disabled:cursor-not-allowed disabled:opacity-30 disabled:hover:text-neutral/40">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                                             stroke-linecap="round" stroke-linejoin="round" class="h-3 w-3">
                                            <polyline points="6 9 12 15 18 9"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>

                            {{-- P3b — tiny image preview thumbnail in the list --}}
                            @if ($q->image)
                                <img src="{{ route('lms.uploads.show', $q->image) }}" alt=""
                                     class="h-12 w-12 shrink-0 rounded border border-neutral/10 object-cover">
                            @endif
                            <div>
                                <div class="text-sm font-medium text-neutral">{{ $q->prompt }}</div>
                                <div class="mt-1 text-xs text-neutral/50">
                                    {{ $q->isTrueFalse() ? 'True / False' : 'Multiple choice' }} · {{ $q->options->count() }} options
                                    @if ($q->image) · <span class="text-neutral/40">with image</span> @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            {{-- P3b — edit payload prepared above in @php block --}}
                            <button type="button"
                                data-edit-question='@json($editPayload)'
                                data-question-id="{{ $q->id }}"
                                data-update-url="{{ route('lms.questions.update', [$lesson, $q]) }}"
                                class="text-sm font-medium text-primary hover:underline">Edit</button>
                            <form method="POST" action="{{ route('lms.questions.destroy', [$lesson, $q]) }}"
                                  onsubmit="return confirm('Delete this question?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p class="rounded-xl border border-dashed border-neutral/20 p-6 text-center text-sm text-neutral/50">
                    No questions yet. Add the first one below.
                </p>
            @endforelse
        </div>

        {{-- Editor --}}
        <div data-question-editor
             data-upload-url="{{ route('lms.uploads.store') }}"
             data-csrf="{{ csrf_token() }}"
             class="mt-8 rounded-xl border border-neutral/10 bg-white p-5">
            <h2 data-q-editor-title class="text-sm font-semibold text-neutral">Add a question</h2>

            <form data-question-form method="POST" action="{{ route('lms.questions.store', $lesson) }}" class="mt-4 space-y-4">
                @csrf
                <input type="hidden" data-q-edit-id>

                <div>
                    <label class="block text-sm font-medium text-neutral">Type</label>
                    <select data-q-type class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm">
                        <option value="mcq">Multiple choice</option>
                        <option value="true_false">True / False</option>
                    </select>
                </div>

                {{-- P3b — image upload UI --}}
                <div>
                    <label class="block text-sm font-medium text-neutral">
                        Image <span class="text-neutral/40">(optional, JPEG/PNG/WebP, max 2MB)</span>
                    </label>

                    <input type="file" data-q-image-input accept="image/jpeg,image/png,image/webp" class="hidden">

                    {{-- "No image" state: just the upload button --}}
                    <div data-q-image-empty class="mt-1">
                        <button type="button" data-q-image-button
                                class="inline-flex items-center gap-2 rounded-lg border border-neutral/20 bg-white px-3 py-2 text-sm font-medium text-neutral hover:border-primary hover:text-primary-dark">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <circle cx="8.5" cy="8.5" r="1.5"/>
                                <polyline points="21 15 16 10 5 21"/>
                            </svg>
                            Add image
                        </button>
                    </div>

                    {{-- "Image attached" state: thumbnail + remove --}}
                    <div data-q-image-present class="mt-1 hidden">
                        <div class="flex items-center gap-3 rounded-lg border border-neutral/20 bg-surface p-2">
                            <img data-q-image-thumb src="" alt=""
                                 class="h-16 w-16 rounded border border-neutral/10 object-cover">
                            <button type="button" data-q-image-remove
                                    class="text-sm font-medium text-red-600 hover:underline">
                                Remove image
                            </button>
                        </div>
                    </div>

                    {{-- Error slot --}}
                    <div data-q-image-error class="mt-2 hidden rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700"></div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral">Question</label>
                    <textarea data-q-prompt rows="2" class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-neutral">Options <span class="text-neutral/40">(select the correct one)</span></label>
                    <div data-q-options class="mt-1 space-y-2"></div>
                    <button type="button" data-q-add-option
                        class="mt-2 text-sm font-medium text-primary hover:underline">+ Add option</button>
                </div>

                <textarea data-question-output name="question" class="hidden"></textarea>

                <button type="submit"
                    class="rounded-lg bg-primary px-4 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                    Save question
                </button>
            </form>
        </div>
    </div>
@endsection
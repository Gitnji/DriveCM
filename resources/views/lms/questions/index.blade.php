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
            @forelse ($questions as $q)
                <div class="rounded-xl border border-neutral/10 bg-white p-4">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="text-sm font-medium text-neutral">{{ $q->prompt }}</div>
                            <div class="mt-1 text-xs text-neutral/50">
                                {{ $q->isTrueFalse() ? 'True / False' : 'Multiple choice' }} · {{ $q->options->count() }} options
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button"
                                data-edit-question='@json($q->only('type','prompt') + ['options' => $q->options->map->only('text','is_correct')])'
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
        <div data-question-editor class="mt-8 rounded-xl border border-neutral/10 bg-white p-5">
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
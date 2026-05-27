@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl">
        <h1 class="text-xl font-semibold text-neutral">Theory Levels</h1>
        <p class="mt-1 text-sm text-neutral/60">Rename the five levels to suit your school.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="mt-6 space-y-4">
            @foreach ($levels as $level)
                <div class="rounded-xl border border-neutral/10 bg-white p-5">
                    <div class="mb-3 text-xs font-medium uppercase tracking-wide text-neutral/40">
                        Level {{ $level->position }}
                    </div>
                    <form method="POST" action="{{ route('lms.levels.update', $level) }}" class="space-y-3">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-sm font-medium text-neutral">Name</label>
                            <input type="text" name="name" value="{{ old('name', $level->name) }}" required maxlength="100"
                                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-neutral">Description <span class="text-neutral/40">(optional)</span></label>
                            <textarea name="description" rows="2" maxlength="1000"
                                class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:ring-1 focus:ring-primary">{{ old('description', $level->description) }}</textarea>
                        </div>

                        <button type="submit"
                            class="rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary-dark">
                            Save
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
@endsection
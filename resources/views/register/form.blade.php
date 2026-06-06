@extends('site.public.layout')

@section('content')
<div class="mx-auto max-w-2xl px-6 py-12">
    <div class="rounded-2xl border border-neutral/10 bg-white p-8 shadow-sm">
        <h1 class="text-3xl font-bold text-neutral">Apply to enroll</h1>
        <p class="mt-2 text-sm text-neutral/60">
            Fill in your details below. We'll review your application and contact you to complete enrollment.
        </p>

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="mt-6 space-y-5">
            @csrf

            {{-- Honeypot: invisible to humans, bots fill it. --}}
            <div aria-hidden="true" style="position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;">
                <label for="website">Website (leave blank)</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="name">Full name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">
                <p class="mt-1 text-xs text-neutral/50">We'll send confirmation here.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="+237 ...">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="town">Town</label>
                <input type="text" id="town" name="town" value="{{ old('town') }}" required
                       class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                       placeholder="Bamenda, Yaoundé, Douala...">
            </div>

            <div>
                <label class="block text-sm font-medium text-neutral" for="notes">
                    Anything you'd like us to know
                    <span class="text-neutral/40">(optional)</span>
                </label>
                <textarea id="notes" name="notes" rows="4"
                          class="mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary">{{ old('notes') }}</textarea>
            </div>

            <button type="submit"
                    class="rounded-lg bg-primary px-6 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Submit application
            </button>
        </form>
    </div>
</div>
@endsection
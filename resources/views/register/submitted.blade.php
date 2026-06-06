@extends('site.public.layout')

@section('content')
<div class="mx-auto max-w-xl px-6 py-12">
    <div class="rounded-2xl border border-success/20 bg-success/5 p-8 text-center">
        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-success/15 text-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="h-8 w-8">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-neutral">Application received</h1>
        <p class="mt-3 text-sm text-neutral/70">
            Thanks for applying. We'll review your application and reach out to confirm next steps.
        </p>
        <a href="/" class="mt-6 inline-block text-sm font-medium text-primary hover:underline">
            ← Back to the website
        </a>
    </div>
</div>
@endsection
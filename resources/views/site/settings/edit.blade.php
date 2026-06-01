@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-xl">
        <h1 class="text-xl font-semibold text-neutral">Appearance</h1>
        <p class="mt-1 text-sm text-neutral/60">How your public site looks. Changes apply immediately to your live site.</p>

        @if (session('status'))
            <div class="mt-4 rounded-lg bg-green-50 px-3 py-2 text-sm text-green-700">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">
                <ul class="list-disc pl-4">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('site.settings.update') }}" enctype="multipart/form-data"
              class="mt-6 space-y-6"
              id="appearance-form"
              data-upload-url="{{ route('lms.uploads.store') }}"
              data-csrf="{{ csrf_token() }}">
            @csrf @method('PUT')

            {{-- LOGO --}}
            <fieldset class="space-y-2">
                <legend class="text-sm font-medium text-neutral">Logo</legend>

                <div id="logo-current">
                    @if (! empty($settings['logo_url']))
                        <img src="{{ $settings['logo_url'] }}" alt="Logo"
                             class="h-16 rounded border border-neutral/10 bg-white p-1">
                    @else
                        <p class="text-xs text-neutral/50">No logo uploaded — your school's name will be shown.</p>
                    @endif
                </div>

                <input type="hidden" name="logo_upload_id" id="logo_upload_id"
                       value="{{ old('logo_upload_id', $settings['record']?->logo_upload_id) }}">

                <input type="file" id="logo-picker" accept="image/jpeg,image/png,image/webp"
                       class="block w-full text-sm">
                <p id="logo-status" class="text-xs text-neutral/50"></p>
            </fieldset>

            {{-- PRIMARY COLOR --}}
            <fieldset>
                <legend class="text-sm font-medium text-neutral">Primary color</legend>
                <p class="mt-1 text-xs text-neutral/50">Used for your site header and buttons.</p>
                <div class="mt-2 flex items-center gap-3">
                    <input type="color" name="primary_color"
                           value="{{ old('primary_color', $settings['primary_color']) }}"
                           class="h-10 w-16 cursor-pointer rounded border border-neutral/20">
                    <span class="text-xs font-mono text-neutral/60">{{ $settings['primary_color'] }}</span>
                </div>
            </fieldset>

            {{-- FOOTER --}}
            <fieldset class="space-y-2">
                <legend class="text-sm font-medium text-neutral">Footer contact info</legend>
                <p class="text-xs text-neutral/50">Pulled from your school's contact details. Toggle which lines visitors see.</p>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="footer_show_email" value="1"
                           @checked(old('footer_show_email', $settings['footer_show_email']))>
                    <span class="text-sm">Show email address</span>
                </label>

                <label class="flex items-center gap-2">
                    <input type="checkbox" name="footer_show_phone" value="1"
                           @checked(old('footer_show_phone', $settings['footer_show_phone']))>
                    <span class="text-sm">Show phone number</span>
                </label>
            </fieldset>

            <button type="submit"
                    class="rounded-lg bg-primary px-5 py-2.5 text-sm font-semibold text-white hover:bg-primary-dark">
                Save appearance
            </button>
        </form>

        {{-- Tiny inline JS to handle the logo upload via the existing endpoint and push the id into the form. --}}
        <script>
            (function () {
                const form = document.getElementById('appearance-form');
                const picker = document.getElementById('logo-picker');
                const status = document.getElementById('logo-status');
                const hiddenId = document.getElementById('logo_upload_id');
                const current = document.getElementById('logo-current');
                if (! form || ! picker) return;

                picker.addEventListener('change', async () => {
                    if (! picker.files.length) return;
                    status.textContent = 'Uploading...';
                    try {
                        const data = new FormData();
                        data.append('image', picker.files[0]);
                        const res = await fetch(form.dataset.uploadUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': form.dataset.csrf, 'Accept': 'application/json' },
                            body: data,
                        });
                        if (! res.ok) {
                            const err = await res.json().catch(() => ({}));
                            status.textContent = 'Upload failed: ' + (err.message || res.status);
                            return;
                        }
                        const json = await res.json();
                        hiddenId.value = json.id;
                        current.innerHTML =
                            '<img src="' + json.url + '" alt="Logo" class="h-16 rounded border border-neutral/10 bg-white p-1">';
                        status.textContent = 'Uploaded — save to apply.';
                    } catch (e) {
                        status.textContent = 'Upload error: ' + e;
                    }
                });
            })();
        </script>
    </div>
@endsection

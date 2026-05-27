@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md">
        <h1 class="text-xl font-semibold text-neutral">Upload test</h1>
        <p class="mt-1 text-sm text-neutral/60">Temporary — verifies the upload endpoint. The block editor replaces this.</p>

        <div class="mt-6 rounded-xl border border-neutral/10 bg-white p-6">
            <input type="file" id="image" accept="image/jpeg,image/png,image/webp"
                class="block w-full text-sm">
            <button id="upload-btn"
                class="mt-4 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-dark">
                Upload
            </button>
            <pre id="result" class="mt-4 overflow-x-auto rounded-lg bg-surface p-3 text-xs text-neutral/70"></pre>
            <div id="preview" class="mt-4"></div>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('upload-btn');
            const result = document.getElementById('result');
            const preview = document.getElementById('preview');

            btn.addEventListener('click', async function () {
                const fileInput = document.getElementById('image');
                if (!fileInput.files.length) { result.textContent = 'Pick a file first.'; return; }

                const data = new FormData();
                data.append('image', fileInput.files[0]);

                result.textContent = 'Uploading...';
                try {
                    const res = await fetch('{{ route('lms.uploads.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: data,
                    });
                    const json = await res.json();
                    result.textContent = 'Status ' + res.status + '\n' + JSON.stringify(json, null, 2);
                    if (json.url) {
                        preview.innerHTML = '<img src="' + json.url + '" class="max-w-full rounded-lg border border-neutral/10">';
                    }
                } catch (e) {
                    result.textContent = 'Error: ' + e;
                }
            });
        })();
    </script>
@endsection
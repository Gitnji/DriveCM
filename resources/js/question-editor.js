// DriveCM question editor — client-side state (D58, full re-render).
// One question at a time: type, prompt, options, one correct. D63/D64/D67.
// P3b — image upload UI added.

export function createQuestionEditor(rootEl) {
    const form        = rootEl.querySelector('[data-question-form]');
    const output      = rootEl.querySelector('[data-question-output]');
    const typeSel     = rootEl.querySelector('[data-q-type]');
    const promptEl    = rootEl.querySelector('[data-q-prompt]');
    const optionsEl   = rootEl.querySelector('[data-q-options]');
    const addBtn      = rootEl.querySelector('[data-q-add-option]');
    const editIdEl    = rootEl.querySelector('[data-q-edit-id]');
    const titleEl     = rootEl.querySelector('[data-q-editor-title]');

    // P3b — image upload elements
    const imageInput   = rootEl.querySelector('[data-q-image-input]');
    const imageButton  = rootEl.querySelector('[data-q-image-button]');
    const imageRemove  = rootEl.querySelector('[data-q-image-remove]');
    const imageEmpty   = rootEl.querySelector('[data-q-image-empty]');
    const imagePresent = rootEl.querySelector('[data-q-image-present]');
    const imageThumb   = rootEl.querySelector('[data-q-image-thumb]');
    const imageError   = rootEl.querySelector('[data-q-image-error]');
    const uploadUrl    = rootEl.dataset.uploadUrl;
    const csrfToken    = rootEl.dataset.csrf;

    let state = blankState();

    function blankState() {
        return {
            type: 'mcq',
            prompt: '',
            options: [{ text: '', is_correct: true }, { text: '', is_correct: false }],
            image_upload_id: null,
            image_url: null, // not persisted — used only for the preview thumbnail
        };
    }

    function render() {
        typeSel.value = state.type;
        promptEl.value = state.prompt;

        optionsEl.innerHTML = '';
        state.options.forEach((opt, i) => optionsEl.appendChild(renderOption(opt, i)));

        addBtn.style.display = state.type === 'true_false' ? 'none' : '';

        renderImage();
        sync();
    }

    function renderImage() {
        if (state.image_upload_id) {
            imageEmpty.classList.add('hidden');
            imagePresent.classList.remove('hidden');
            imageThumb.src = state.image_url || '';
        } else {
            imageEmpty.classList.remove('hidden');
            imagePresent.classList.add('hidden');
            imageThumb.src = '';
        }
    }

    function renderOption(opt, index) {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';

        const radio = document.createElement('input');
        radio.type = 'radio';
        radio.name = 'q-correct';
        radio.checked = opt.is_correct;
        radio.addEventListener('change', () => {
            state.options.forEach((o, i) => { o.is_correct = (i === index); });
            sync();
        });

        const text = document.createElement('input');
        text.type = 'text';
        text.value = opt.text;
        text.className = 'flex-1 rounded-lg border border-neutral/20 px-3 py-2 text-sm';
        if (state.type === 'true_false') {
            text.readOnly = true;
            text.classList.add('bg-surface', 'text-neutral/60');
        }
        text.addEventListener('input', () => { state.options[index].text = text.value; sync(); });

        row.append(radio, text);

        if (state.type === 'mcq' && state.options.length > 2) {
            const rm = document.createElement('button');
            rm.type = 'button';
            rm.textContent = '✕';
            rm.className = 'rounded px-2 py-1 text-sm text-red-600 hover:bg-red-50';
            rm.addEventListener('click', () => {
                state.options.splice(index, 1);
                if (!state.options.some((o) => o.is_correct)) {
                    state.options[0].is_correct = true;
                }
                render();
            });
            row.appendChild(rm);
        }

        return row;
    }

    function sync() {
        // P3b — payload includes image_upload_id (server uses this). image_url is preview-only,
        // intentionally excluded so it doesn't bloat the persisted state.
        const persisted = {
            type: state.type,
            prompt: state.prompt,
            options: state.options,
            image_upload_id: state.image_upload_id,
        };
        output.value = JSON.stringify(persisted);
    }

    // Type switch (D67).
    typeSel.addEventListener('change', () => {
        state.type = typeSel.value;
        if (state.type === 'true_false') {
            state.options = [
                { text: 'True', is_correct: true },
                { text: 'False', is_correct: false },
            ];
        } else {
            while (state.options.length < 2) {
                state.options.push({ text: '', is_correct: false });
            }
        }
        render();
    });

    promptEl.addEventListener('input', () => { state.prompt = promptEl.value; sync(); });

    addBtn.addEventListener('click', () => {
        if (state.options.length >= 6) return;
        state.options.push({ text: '', is_correct: false });
        render();
    });

    // ---- P3b — image upload ----

    function showImageError(msg) {
        imageError.textContent = msg;
        imageError.classList.remove('hidden');
    }
    function clearImageError() {
        imageError.textContent = '';
        imageError.classList.add('hidden');
    }

    imageButton.addEventListener('click', () => imageInput.click());

    imageInput.addEventListener('change', async () => {
        const file = imageInput.files?.[0];
        if (!file) return;

        // Client-side sanity checks (server enforces too, but better UX).
        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (!allowed.includes(file.type)) {
            showImageError('Please use a JPEG, PNG, or WebP image.');
            imageInput.value = '';
            return;
        }
        if (file.size > 2 * 1024 * 1024) {
            showImageError('That image is too large. Maximum 2 MB.');
            imageInput.value = '';
            return;
        }

        clearImageError();

        // Uploading state (A — button change, no spinner library).
        const originalLabel = imageButton.innerHTML;
        imageButton.disabled = true;
        imageButton.innerHTML = 'Uploading…';

        try {
            const formData = new FormData();
            formData.append('image', file);

            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData,
            });

            if (!res.ok) {
                // Try to read a server error message.
                let msg = 'Upload failed.';
                try {
                    const err = await res.json();
                    if (err?.message) msg = err.message;
                } catch (e) { /* response wasn't JSON */ }
                showImageError(msg);
                return;
            }

            const data = await res.json();
            state.image_upload_id = data.id;
            state.image_url = data.url;
            renderImage();
            sync();
        } catch (err) {
            showImageError('Upload failed. Check your connection and try again.');
        } finally {
            imageButton.disabled = false;
            imageButton.innerHTML = originalLabel;
            imageInput.value = ''; // reset so picking the same file again re-fires change
        }
    });

    imageRemove.addEventListener('click', () => {
        state.image_upload_id = null;
        state.image_url = null;
        clearImageError();
        renderImage();
        sync();
    });

    // ---- Edit: load an existing question into the editor ----

    rootEl.querySelectorAll('[data-edit-question]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const loaded = JSON.parse(btn.dataset.editQuestion);
            // P3b — fold in image fields with safe defaults.
            state = {
                type: loaded.type,
                prompt: loaded.prompt,
                options: loaded.options,
                image_upload_id: loaded.image_upload_id ?? null,
                image_url: loaded.image_url ?? null,
            };
            editIdEl.value = btn.dataset.questionId;
            titleEl.textContent = 'Edit question';
            form.action = btn.dataset.updateUrl;
            let m = form.querySelector('input[name="_method"]');
            if (!m) {
                m = document.createElement('input');
                m.type = 'hidden';
                m.name = '_method';
                form.appendChild(m);
            }
            m.value = 'PUT';
            clearImageError();
            render();
            rootEl.scrollIntoView({ behavior: 'smooth' });
        });
    });

    render();
}
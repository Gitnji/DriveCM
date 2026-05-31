// DriveCM page block editor (CMS-2b, D127/D133).
// Copies the lesson block-editor pattern (D58) with page block types: hero / rich_text / image.

import Quill from 'quill';

const QUILL_TOOLBAR = [
    ['bold', 'italic', 'underline'],
    [{ header: 2 }, { header: 3 }],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'link'],
    ['clean'],
];

export function createPageEditor(rootEl, initialBlocks = []) {
    const listEl = rootEl.querySelector('[data-block-list]');
    const outputEl = rootEl.querySelector('[data-block-output]');
    const uploadUrl = rootEl.dataset.uploadUrl;
    const csrf = rootEl.dataset.csrf;

    const blocks = [];

    function sync() {
        outputEl.value = JSON.stringify(blocks.map((b) => b.data));
    }

    function reorderDom() {
        blocks.forEach((b, i) => {
            listEl.appendChild(b.el);
            refreshControls(b, i);
        });
        sync();
    }

    function refreshControls(b, index) {
        const up = b.el.querySelector('[data-up]');
        const down = b.el.querySelector('[data-down]');
        if (up) up.disabled = index === 0;
        if (down) down.disabled = index === blocks.length - 1;
    }

    function buildBlockEl(entry) {
        const wrap = document.createElement('div');
        wrap.className = 'rounded-lg border border-neutral/15 bg-white p-3';

        const header = document.createElement('div');
        header.className = 'mb-2 flex items-center justify-between';
        header.innerHTML =
            `<span class="text-xs font-medium uppercase tracking-wide text-neutral/40">${entry.data.type}</span>`;

        const controls = document.createElement('div');
        controls.className = 'flex items-center gap-2';
        controls.append(
            ctrlBtn('↑', 'data-up', () => move(entry, -1)),
            ctrlBtn('↓', 'data-down', () => move(entry, 1)),
            ctrlBtn('✕', 'data-del', () => removeBlock(entry)),
        );
        header.appendChild(controls);
        wrap.appendChild(header);

        wrap.appendChild(buildBlockBody(entry));
        return wrap;
    }

    function buildBlockBody(entry) {
        const { type } = entry.data;

        if (type === 'hero') return buildHeroBody(entry);
        if (type === 'rich_text') return buildRichTextBody(entry);
        if (type === 'image') {
            const holder = document.createElement('div');
            renderImageBody(entry, holder);
            return holder;
        }

        const ph = document.createElement('div');
        ph.className = 'text-xs text-neutral/40';
        ph.textContent = `[${type} block — unknown]`;
        return ph;
    }

    function buildHeroBody(entry) {
        const holder = document.createElement('div');
        holder.className = 'space-y-2';

        holder.appendChild(textInput('Heading', entry.data.heading || '', (v) => { entry.data.heading = v; sync(); }));
        holder.appendChild(textArea('Subtext', entry.data.subtext || '', (v) => { entry.data.subtext = v; sync(); }));

        // Background image — re-uses the image upload state pattern.
        const bgWrap = document.createElement('div');
        bgWrap.className = 'rounded-lg border border-dashed border-neutral/20 p-2';
        const bgLabel = document.createElement('div');
        bgLabel.className = 'mb-1 text-xs font-medium text-neutral/60';
        bgLabel.textContent = 'Background image';
        bgWrap.appendChild(bgLabel);
        const bgHolder = document.createElement('div');
        bgWrap.appendChild(bgHolder);
        renderBackgroundBody(entry, bgHolder);
        holder.appendChild(bgWrap);

        holder.appendChild(textInput('Button label (CTA)', entry.data.cta_text || '', (v) => { entry.data.cta_text = v; sync(); }));
        holder.appendChild(textInput('Button URL', entry.data.cta_url || '', (v) => { entry.data.cta_url = v; sync(); }));

        return holder;
    }

    function renderBackgroundBody(entry, holder) {
        holder.innerHTML = '';

        if (!entry.data.background_url) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp';
            input.className = 'block w-full text-sm';

            const status = document.createElement('div');
            status.className = 'mt-2 text-xs text-neutral/50';

            input.addEventListener('change', async () => {
                if (!input.files.length) return;
                status.textContent = 'Uploading...';
                try {
                    const data = new FormData();
                    data.append('image', input.files[0]);
                    const res = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: data,
                    });
                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        status.textContent = 'Upload failed: ' + (err.message || res.status);
                        return;
                    }
                    const json = await res.json();
                    entry.data.background_url = json.url;
                    sync();
                    renderBackgroundBody(entry, holder);
                } catch (e) {
                    status.textContent = 'Upload error: ' + e;
                }
            });

            holder.append(input, status);
            return;
        }

        const img = document.createElement('img');
        img.src = entry.data.background_url;
        img.className = 'max-h-32 rounded-lg border border-neutral/10';

        const replace = document.createElement('button');
        replace.type = 'button';
        replace.textContent = 'Replace background';
        replace.className = 'mt-2 block text-xs font-medium text-primary hover:underline';
        replace.addEventListener('click', () => {
            entry.data.background_url = '';
            sync();
            renderBackgroundBody(entry, holder);
        });

        holder.append(img, replace);
    }

    function buildRichTextBody(entry) {
        const editorEl = document.createElement('div');
        const holder = document.createElement('div');
        holder.appendChild(editorEl);

        queueMicrotask(() => {
            const quill = new Quill(editorEl, {
                theme: 'snow',
                modules: { toolbar: QUILL_TOOLBAR },
            });
            if (entry.data.html) {
                quill.clipboard.dangerouslyPasteHTML(entry.data.html);
            }
            quill.on('text-change', () => {
                const html = quill.root.innerHTML;
                entry.data.html = (html === '<p><br></p>') ? '' : html;
                sync();
            });
            entry.quill = quill;
        });
        return holder;
    }

    function renderImageBody(entry, holder) {
        holder.innerHTML = '';

        if (!entry.data.url) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp';
            input.className = 'block w-full text-sm';

            const status = document.createElement('div');
            status.className = 'mt-2 text-xs text-neutral/50';

            input.addEventListener('change', async () => {
                if (!input.files.length) return;
                status.textContent = 'Uploading...';
                try {
                    const data = new FormData();
                    data.append('image', input.files[0]);
                    const res = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: data,
                    });
                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        status.textContent = 'Upload failed: ' + (err.message || res.status);
                        return;
                    }
                    const json = await res.json();
                    entry.data.url = json.url;
                    sync();
                    renderImageBody(entry, holder);
                } catch (e) {
                    status.textContent = 'Upload error: ' + e;
                }
            });

            holder.append(input, status);
            return;
        }

        const img = document.createElement('img');
        img.src = entry.data.url;
        img.className = 'max-h-48 rounded-lg border border-neutral/10';

        const altInput = document.createElement('input');
        altInput.type = 'text';
        altInput.placeholder = 'Describe this image (alt text)';
        altInput.value = entry.data.alt || '';
        altInput.className = 'mt-2 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm';
        altInput.addEventListener('input', () => { entry.data.alt = altInput.value; sync(); });

        const captionInput = document.createElement('input');
        captionInput.type = 'text';
        captionInput.placeholder = 'Caption (shown below the image, optional)';
        captionInput.value = entry.data.caption || '';
        captionInput.className = 'mt-2 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm';
        captionInput.addEventListener('input', () => { entry.data.caption = captionInput.value; sync(); });

        const replace = document.createElement('button');
        replace.type = 'button';
        replace.textContent = 'Replace image';
        replace.className = 'mt-2 text-xs font-medium text-primary hover:underline';
        replace.addEventListener('click', () => {
            entry.data.url = '';
            sync();
            renderImageBody(entry, holder);
        });

        holder.append(img, altInput, captionInput, replace);
    }

    function textInput(label, initial, onInput) {
        const wrap = document.createElement('label');
        wrap.className = 'block';
        const lbl = document.createElement('span');
        lbl.className = 'block text-xs font-medium text-neutral/60';
        lbl.textContent = label;
        const inp = document.createElement('input');
        inp.type = 'text';
        inp.value = initial;
        inp.className = 'mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm';
        inp.addEventListener('input', () => onInput(inp.value));
        wrap.append(lbl, inp);
        return wrap;
    }

    function textArea(label, initial, onInput) {
        const wrap = document.createElement('label');
        wrap.className = 'block';
        const lbl = document.createElement('span');
        lbl.className = 'block text-xs font-medium text-neutral/60';
        lbl.textContent = label;
        const inp = document.createElement('textarea');
        inp.rows = 2;
        inp.value = initial;
        inp.className = 'mt-1 w-full rounded-lg border border-neutral/20 px-3 py-2 text-sm';
        inp.addEventListener('input', () => onInput(inp.value));
        wrap.append(lbl, inp);
        return wrap;
    }

    function ctrlBtn(label, marker, onClick) {
        const b = document.createElement('button');
        b.type = 'button';
        b.textContent = label;
        b.setAttribute(marker, '');
        b.className = 'rounded px-2 py-1 text-sm text-neutral/60 hover:bg-surface disabled:opacity-30';
        b.addEventListener('click', onClick);
        return b;
    }

    function addBlock(type) {
        const fresh = {
            hero:      { type: 'hero', heading: '', subtext: '', cta_text: '', cta_url: '', background_url: '' },
            rich_text: { type: 'rich_text', html: '' },
            image:     { type: 'image', url: '', alt: '', caption: '' },
        }[type];
        const entry = { data: JSON.parse(JSON.stringify(fresh)), el: null, quill: null };
        entry.el = buildBlockEl(entry);
        blocks.push(entry);
        reorderDom();
    }

    function removeBlock(entry) {
        const i = blocks.indexOf(entry);
        if (i === -1) return;
        if (entry.quill) entry.quill = null;
        entry.el.remove();
        blocks.splice(i, 1);
        reorderDom();
    }

    function move(entry, delta) {
        const i = blocks.indexOf(entry);
        const target = i + delta;
        if (target < 0 || target >= blocks.length) return;
        [blocks[i], blocks[target]] = [blocks[target], blocks[i]];
        reorderDom();
    }

    rootEl.querySelectorAll('[data-add-block]').forEach((btn) => {
        btn.addEventListener('click', () => addBlock(btn.dataset.addBlock));
    });

    initialBlocks.forEach((data) => {
        const entry = { data: JSON.parse(JSON.stringify(data)), el: null, quill: null };
        entry.el = buildBlockEl(entry);
        blocks.push(entry);
    });
    reorderDom();

    return { getData: () => blocks.map((b) => b.data) };
}
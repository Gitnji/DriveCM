// DriveCM page block editor (CMS-2b/5a/5b/5b.5 — D127/D133/D142/D144/D145/D146/D147/D148).
import { PAGE_ICONS, PAGE_ICON_KEYS } from './page-icons.js';
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
        if (type === 'cta') return buildCtaBody(entry);
        if (type === 'gallery') return buildGalleryBody(entry);
        if (type === 'cards') return buildCardsBody(entry);
        if (type === 'instructors') return buildInstructorsBody(entry);
        if (type === 'pricing') return buildPricingBody(entry);
        if (type === 'contact_map') return buildContactMapBody(entry);


        const ph = document.createElement('div');
        ph.className = 'text-xs text-neutral/40';
        ph.textContent = `[${type} block — unknown]`;
        return ph;
    }

    // -----------------------------------------------------------------
    // D146/D148 — createNestedList: reusable nested-list editor.
    // Each child gets a DOM row that lives for the child's lifetime —
    // reorder appends existing nodes, not rebuilds them. This means text
    // inputs inside a sub-item keep focus across reorders and across
    // every keystroke (same persistent-node pattern as the outer block
    // list, D58).
    //
    // childData and rowEl stay in lockstep: entry.data.children[i] and
    // childNodes[i] always describe the same item.
    //
    // - addItemLabel: text for the "+ Add" button
    // - newItemData(): factory returning a fresh child object
    // - itemRenderer(childData, onChange): returns the per-item editor
    //   element. onChange() must be called whenever childData mutates.
    // -----------------------------------------------------------------
    function createNestedList(entry, { addItemLabel, newItemData, itemRenderer }) {
        if (! Array.isArray(entry.data.children)) {
            entry.data.children = [];
        }

        const wrap = document.createElement('div');
        wrap.className = 'space-y-2';

        const list = document.createElement('div');
        list.className = 'space-y-2';
        wrap.appendChild(list);

        const addBtn = document.createElement('button');
        addBtn.type = 'button';
        addBtn.textContent = addItemLabel;
        addBtn.className = 'rounded-lg border border-dashed border-neutral/30 px-3 py-2 text-sm font-medium text-neutral/70 hover:bg-surface';
        wrap.appendChild(addBtn);

        // childNodes[i] is the row element for entry.data.children[i].
        const childNodes = [];

        function buildRow(child) {
            const row = document.createElement('div');
            row.className = 'rounded border border-neutral/15 bg-surface p-2';

            const head = document.createElement('div');
            head.className = 'mb-2 flex items-center justify-between text-xs text-neutral/50';
            const label = document.createElement('span');
            label.dataset.itemLabel = '';
            head.appendChild(label);

            const ctrls = document.createElement('div');
            ctrls.className = 'flex items-center gap-1';

            const upBtn = ctrlBtn('↑', '', () => moveByRow(row, -1));
            upBtn.dataset.up = '';
            const downBtn = ctrlBtn('↓', '', () => moveByRow(row, 1));
            downBtn.dataset.down = '';
            const delBtn = ctrlBtn('✕', '', () => removeByRow(row));

            ctrls.append(upBtn, downBtn, delBtn);
            head.appendChild(ctrls);
            row.appendChild(head);

            row.appendChild(itemRenderer(child, sync));
            return row;
        }

        function refresh() {
            childNodes.forEach((row, i) => {
                row.querySelector('[data-item-label]').textContent = `Item ${i + 1}`;
                row.querySelector('[data-up]').disabled = (i === 0);
                row.querySelector('[data-down]').disabled = (i === childNodes.length - 1);
            });
        }

        function reorderChildDom() {
            childNodes.forEach((row) => list.appendChild(row));
            refresh();
        }

        function moveByRow(row, delta) {
            const i = childNodes.indexOf(row);
            const target = i + delta;
            if (i === -1 || target < 0 || target >= childNodes.length) return;
            const data = entry.data.children;
            [data[i], data[target]] = [data[target], data[i]];
            [childNodes[i], childNodes[target]] = [childNodes[target], childNodes[i]];
            reorderChildDom();
            sync();
        }

        function removeByRow(row) {
            const i = childNodes.indexOf(row);
            if (i === -1) return;
            entry.data.children.splice(i, 1);
            childNodes.splice(i, 1);
            row.remove();
            refresh();
            sync();
        }

        addBtn.addEventListener('click', () => {
            const child = JSON.parse(JSON.stringify(newItemData()));
            entry.data.children.push(child);
            const row = buildRow(child);
            childNodes.push(row);
            list.appendChild(row);
            refresh();
            sync();
        });

        // Hydrate from existing children.
        entry.data.children.forEach((child) => {
            const row = buildRow(child);
            childNodes.push(row);
            list.appendChild(row);
        });
        refresh();

        return wrap;
    }

    // CMS-5b (D147) — gallery uses createNestedList with image-style sub-items.
    function buildGalleryBody(entry) {
        return createNestedList(entry, {
            addItemLabel: '+ Add image',
            newItemData: () => ({ url: '', alt: '' }),
            itemRenderer: (child, onChange) => galleryItemEditor(child, onChange),
        });
    }

    // Editor for one gallery sub-item: picker / preview + alt.
    function galleryItemEditor(child, onChange) {
        const holder = document.createElement('div');
        render();
        return holder;

        function render() {
            holder.innerHTML = '';

            if (! child.url) {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/jpeg,image/png,image/webp';
                input.className = 'block w-full text-sm';

                const status = document.createElement('div');
                status.className = 'mt-2 text-xs text-neutral/50';

                input.addEventListener('change', async () => {
                    if (! input.files.length) return;
                    status.textContent = 'Uploading...';
                    try {
                        const data = new FormData();
                        data.append('image', input.files[0]);
                        const res = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: data,
                        });
                        if (! res.ok) {
                            const err = await res.json().catch(() => ({}));
                            status.textContent = 'Upload failed: ' + (err.message || res.status);
                            return;
                        }
                        const json = await res.json();
                        child.url = json.url;
                        onChange();
                        render();
                    } catch (e) {
                        status.textContent = 'Upload error: ' + e;
                    }
                });

                holder.append(input, status);
                return;
            }

            const img = document.createElement('img');
            img.src = child.url;
            img.className = 'max-h-32 rounded border border-neutral/10';

            const altInput = document.createElement('input');
            altInput.type = 'text';
            altInput.placeholder = 'Describe this image (alt text)';
            altInput.value = child.alt || '';
            altInput.className = 'mt-2 w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
            altInput.addEventListener('input', () => { child.alt = altInput.value; onChange(); });

            const replace = document.createElement('button');
            replace.type = 'button';
            replace.textContent = 'Replace';
            replace.className = 'mt-2 text-xs font-medium text-primary hover:underline';
            replace.addEventListener('click', () => {
                child.url = '';
                onChange();
                render();
            });

            holder.append(img, altInput, replace);
        }
    }

    // CMS-5c (D149) — cards: nested list of {icon, heading, text}.
    function buildCardsBody(entry) {
        return createNestedList(entry, {
            addItemLabel: '+ Add card',
            newItemData: () => ({ icon: 'check', heading: '', text: '' }),
            itemRenderer: (child, onChange) => cardItemEditor(child, onChange),
        });
    }

    // Editor for one card sub-item: icon dropdown + heading + text.
    function cardItemEditor(child, onChange) {
        const holder = document.createElement('div');
        holder.className = 'space-y-2';

        // Icon row: dropdown + live preview.
        const iconRow = document.createElement('div');
        iconRow.className = 'flex items-center gap-2';

        const iconLabel = document.createElement('span');
        iconLabel.className = 'text-xs font-medium text-neutral/60';
        iconLabel.textContent = 'Icon';

        const iconSelect = document.createElement('select');
        iconSelect.className = 'rounded border border-neutral/20 px-2 py-1.5 text-sm';
        PAGE_ICON_KEYS.forEach((key) => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = key;
            if (child.icon === key) opt.selected = true;
            iconSelect.appendChild(opt);
        });

        const iconPreview = document.createElement('span');
        iconPreview.className = 'inline-flex h-7 w-7 items-center justify-center text-primary';
        iconPreview.innerHTML = PAGE_ICONS[child.icon] || '';

        iconSelect.addEventListener('change', () => {
            child.icon = iconSelect.value;
            iconPreview.innerHTML = PAGE_ICONS[child.icon] || '';
            onChange();
        });

        iconRow.append(iconLabel, iconSelect, iconPreview);
        holder.appendChild(iconRow);

        // Heading.
        const headingWrap = document.createElement('label');
        headingWrap.className = 'block';
        const headingLbl = document.createElement('span');
        headingLbl.className = 'block text-xs font-medium text-neutral/60';
        headingLbl.textContent = 'Heading';
        const headingInp = document.createElement('input');
        headingInp.type = 'text';
        headingInp.value = child.heading || '';
        headingInp.className = 'mt-1 w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
        headingInp.addEventListener('input', () => { child.heading = headingInp.value; onChange(); });
        headingWrap.append(headingLbl, headingInp);
        holder.appendChild(headingWrap);

        // Text.
        const textWrap = document.createElement('label');
        textWrap.className = 'block';
        const textLbl = document.createElement('span');
        textLbl.className = 'block text-xs font-medium text-neutral/60';
        textLbl.textContent = 'Text';
        const textInp = document.createElement('textarea');
        textInp.rows = 2;
        textInp.value = child.text || '';
        textInp.className = 'mt-1 w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
        textInp.addEventListener('input', () => { child.text = textInp.value; onChange(); });
        textWrap.append(textLbl, textInp);
        holder.appendChild(textWrap);

        return holder;
    }

    // CMS-5e (D151) — instructors: nested list of {photo_url, name, role, bio}.
    function buildInstructorsBody(entry) {
        return createNestedList(entry, {
            addItemLabel: '+ Add instructor',
            newItemData: () => ({ photo_url: '', name: '', role: '', bio: '' }),
            itemRenderer: (child, onChange) => instructorItemEditor(child, onChange),
        });
    }

    // CMS-5f (D152) — contact / map. Plain text fields; map iframe rendered server-side from address.
    function buildContactMapBody(entry) {
        const holder = document.createElement('div');
        holder.className = 'space-y-2';

        holder.appendChild(textInput('Section heading (e.g. "Visit us")',
            entry.data.heading || '',
            (v) => { entry.data.heading = v; sync(); }));

        holder.appendChild(textArea('Address (used for the map embed too)',
            entry.data.address || '',
            (v) => { entry.data.address = v; sync(); }));

        const row = document.createElement('div');
        row.className = 'grid grid-cols-2 gap-2';
        row.append(
            textInput('Phone (optional)', entry.data.phone || '', (v) => { entry.data.phone = v; sync(); }),
            textInput('Email (optional)', entry.data.email || '', (v) => { entry.data.email = v; sync(); }),
        );
        holder.appendChild(row);

        holder.appendChild(textArea('Opening hours (optional, one line per day)',
            entry.data.hours || '',
            (v) => { entry.data.hours = v; sync(); }));

        return holder;
    }

    // Editor for one instructor: photo + name + role + bio.
    // The photo follows gallery's image-upload pattern (empty=picker, filled=preview+replace).
    function instructorItemEditor(child, onChange) {
        const holder = document.createElement('div');
        holder.className = 'space-y-2';

        // Photo (re-renders on upload/replace only — same gate as gallery item).
        const photoHolder = document.createElement('div');
        instructorPhotoEditor(child, photoHolder, onChange);
        holder.appendChild(photoHolder);

        // Name (persistent input — focus survives keystrokes).
        const nameWrap = document.createElement('label');
        nameWrap.className = 'block';
        const nameLbl = document.createElement('span');
        nameLbl.className = 'block text-xs font-medium text-neutral/60';
        nameLbl.textContent = 'Name';
        const nameInp = document.createElement('input');
        nameInp.type = 'text';
        nameInp.value = child.name || '';
        nameInp.className = 'mt-1 w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
        nameInp.addEventListener('input', () => { child.name = nameInp.value; onChange(); });
        nameWrap.append(nameLbl, nameInp);
        holder.appendChild(nameWrap);

        // Role.
        const roleWrap = document.createElement('label');
        roleWrap.className = 'block';
        const roleLbl = document.createElement('span');
        roleLbl.className = 'block text-xs font-medium text-neutral/60';
        roleLbl.textContent = 'Role (e.g. Senior Instructor)';
        const roleInp = document.createElement('input');
        roleInp.type = 'text';
        roleInp.value = child.role || '';
        roleInp.className = 'mt-1 w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
        roleInp.addEventListener('input', () => { child.role = roleInp.value; onChange(); });
        roleWrap.append(roleLbl, roleInp);
        holder.appendChild(roleWrap);

        // Bio (textarea, D151.1).
        const bioWrap = document.createElement('label');
        bioWrap.className = 'block';
        const bioLbl = document.createElement('span');
        bioLbl.className = 'block text-xs font-medium text-neutral/60';
        bioLbl.textContent = 'Bio';
        const bioInp = document.createElement('textarea');
        bioInp.rows = 3;
        bioInp.value = child.bio || '';
        bioInp.className = 'mt-1 w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
        bioInp.addEventListener('input', () => { child.bio = bioInp.value; onChange(); });
        bioWrap.append(bioLbl, bioInp);
        holder.appendChild(bioWrap);

        return holder;
    }

    // Instructor photo editor — empty (picker) / filled (preview + replace).
    function instructorPhotoEditor(child, holder, onChange) {
        function render() {
            holder.innerHTML = '';

            if (! child.photo_url) {
                const input = document.createElement('input');
                input.type = 'file';
                input.accept = 'image/jpeg,image/png,image/webp';
                input.className = 'block w-full text-sm';

                const status = document.createElement('div');
                status.className = 'mt-2 text-xs text-neutral/50';

                input.addEventListener('change', async () => {
                    if (! input.files.length) return;
                    status.textContent = 'Uploading...';
                    try {
                        const data = new FormData();
                        data.append('image', input.files[0]);
                        const res = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: data,
                        });
                        if (! res.ok) {
                            const err = await res.json().catch(() => ({}));
                            status.textContent = 'Upload failed: ' + (err.message || res.status);
                            return;
                        }
                        const json = await res.json();
                        child.photo_url = json.url;
                        onChange();
                        render();
                    } catch (e) {
                        status.textContent = 'Upload error: ' + e;
                    }
                });

                holder.append(input, status);
                return;
            }

            const img = document.createElement('img');
            img.src = child.photo_url;
            img.className = 'h-24 w-24 rounded-full object-cover border border-neutral/10';

            const replace = document.createElement('button');
            replace.type = 'button';
            replace.textContent = 'Replace photo';
            replace.className = 'mt-2 block text-xs font-medium text-primary hover:underline';
            replace.addEventListener('click', () => {
                child.photo_url = '';
                onChange();
                render();
            });

            holder.append(img, replace);
        }

        render();
    }

    // CMS-5d (D150) — pricing: nested list of tiers, each with its own nested list of features.
    function buildPricingBody(entry) {
        return createNestedList(entry, {
            addItemLabel: '+ Add pricing tier',
            newItemData: () => ({
                icon: 'check',
                title: '',
                price: '',
                period: '',
                cta_text: '',
                cta_url: '',
                children: [],
            }),
            itemRenderer: (child, onChange) => pricingItemEditor(child, onChange),
        });
    }

    // Editor for one pricing tier: icon + title + price + period + features (inner nested list)
    // + cta_text + cta_url. The features sub-list reuses createNestedList with a string-only
    // child shape ({text}).
    function pricingItemEditor(child, onChange) {
        const holder = document.createElement('div');
        holder.className = 'space-y-3';

        // Icon row (matches cards' icon UI).
        const iconRow = document.createElement('div');
        iconRow.className = 'flex items-center gap-2';
        const iconLabel = document.createElement('span');
        iconLabel.className = 'text-xs font-medium text-neutral/60';
        iconLabel.textContent = 'Icon';
        const iconSelect = document.createElement('select');
        iconSelect.className = 'rounded border border-neutral/20 px-2 py-1.5 text-sm';
        PAGE_ICON_KEYS.forEach((key) => {
            const opt = document.createElement('option');
            opt.value = key;
            opt.textContent = key;
            if (child.icon === key) opt.selected = true;
            iconSelect.appendChild(opt);
        });
        const iconPreview = document.createElement('span');
        iconPreview.className = 'inline-flex h-7 w-7 items-center justify-center text-primary';
        iconPreview.innerHTML = PAGE_ICONS[child.icon] || '';
        iconSelect.addEventListener('change', () => {
            child.icon = iconSelect.value;
            iconPreview.innerHTML = PAGE_ICONS[child.icon] || '';
            onChange();
        });
        iconRow.append(iconLabel, iconSelect, iconPreview);
        holder.appendChild(iconRow);

        // Title, Price, Period — three text inputs.
        holder.appendChild(textInput('Title', child.title || '', (v) => { child.title = v; onChange(); }));

        const priceRow = document.createElement('div');
        priceRow.className = 'grid grid-cols-2 gap-2';
        priceRow.append(
            textInput('Price', child.price || '', (v) => { child.price = v; onChange(); }),
            textInput('Period (e.g. /course)', child.period || '', (v) => { child.period = v; onChange(); }),
        );
        holder.appendChild(priceRow);

        // Features — inner nested list of {text} items.
        const featuresLabel = document.createElement('div');
        featuresLabel.className = 'text-xs font-medium text-neutral/60';
        featuresLabel.textContent = 'Features (what\'s included)';
        holder.appendChild(featuresLabel);

        // Synthetic "entry"-like object so createNestedList works on child.children without
        // accidentally writing to the outer pricing block's children. createNestedList only
        // reads/writes entry.data.children.
        const featuresHost = { data: child };
        holder.appendChild(createNestedList(featuresHost, {
            addItemLabel: '+ Add feature',
            newItemData: () => ({ text: '' }),
            itemRenderer: (feature, innerOnChange) => featureItemEditor(feature, innerOnChange),
        }));

        // CTA row.
        const ctaRow = document.createElement('div');
        ctaRow.className = 'grid grid-cols-2 gap-2';
        ctaRow.append(
            textInput('Button label (optional)', child.cta_text || '', (v) => { child.cta_text = v; onChange(); }),
            textInput('Button URL (optional)', child.cta_url || '', (v) => { child.cta_url = v; onChange(); }),
        );
        holder.appendChild(ctaRow);

        return holder;
    }

    // One feature row: a single text input. Persistent across reorders.
    function featureItemEditor(feature, onChange) {
        const inp = document.createElement('input');
        inp.type = 'text';
        inp.value = feature.text || '';
        inp.placeholder = 'e.g. 10 hours of practical training';
        inp.className = 'w-full rounded border border-neutral/20 px-2 py-1.5 text-sm';
        inp.addEventListener('input', () => { feature.text = inp.value; onChange(); });
        return inp;
    }

    function buildHeroBody(entry) {
        const holder = document.createElement('div');
        holder.className = 'space-y-2';

        holder.appendChild(textInput('Heading', entry.data.heading || '', (v) => { entry.data.heading = v; sync(); }));
        holder.appendChild(textArea('Subtext', entry.data.subtext || '', (v) => { entry.data.subtext = v; sync(); }));

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

    function buildCtaBody(entry) {
        const holder = document.createElement('div');
        holder.className = 'space-y-2';

        holder.appendChild(textInput('Heading', entry.data.heading || '', (v) => { entry.data.heading = v; sync(); }));
        holder.appendChild(textArea('Subtext', entry.data.subtext || '', (v) => { entry.data.subtext = v; sync(); }));
        holder.appendChild(textInput('Button label', entry.data.button_text || '', (v) => { entry.data.button_text = v; sync(); }));
        holder.appendChild(textInput('Button URL', entry.data.button_url || '', (v) => { entry.data.button_url = v; sync(); }));

        const colorWrap = document.createElement('div');
        const colorLabel = document.createElement('span');
        colorLabel.className = 'block text-xs font-medium text-neutral/60';
        colorLabel.textContent = 'Background color (optional — uses your site color if left blank)';
        colorWrap.appendChild(colorLabel);

        const colorRow = document.createElement('div');
        colorRow.className = 'mt-1 flex items-center gap-2';

        const colorInput = document.createElement('input');
        colorInput.type = 'color';
        colorInput.value = entry.data.background_color || '#0A3D62';
        colorInput.className = 'h-9 w-12 cursor-pointer rounded border border-neutral/20';

        const colorText = document.createElement('span');
        colorText.className = 'text-xs font-mono text-neutral/60';
        colorText.textContent = entry.data.background_color || '(default)';

        const clearBtn = document.createElement('button');
        clearBtn.type = 'button';
        clearBtn.textContent = 'Use site color';
        clearBtn.className = 'text-xs font-medium text-primary hover:underline';
        clearBtn.style.display = entry.data.background_color ? 'inline' : 'none';

        colorInput.addEventListener('input', () => {
            entry.data.background_color = colorInput.value;
            colorText.textContent = colorInput.value;
            clearBtn.style.display = 'inline';
            sync();
        });
        clearBtn.addEventListener('click', () => {
            entry.data.background_color = '';
            colorText.textContent = '(default)';
            clearBtn.style.display = 'none';
            sync();
        });

        colorRow.append(colorInput, colorText, clearBtn);
        colorWrap.appendChild(colorRow);
        holder.appendChild(colorWrap);

        return holder;
    }

    function renderBackgroundBody(entry, holder) {
        holder.innerHTML = '';

        if (! entry.data.background_url) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp';
            input.className = 'block w-full text-sm';

            const status = document.createElement('div');
            status.className = 'mt-2 text-xs text-neutral/50';

            input.addEventListener('change', async () => {
                if (! input.files.length) return;
                status.textContent = 'Uploading...';
                try {
                    const data = new FormData();
                    data.append('image', input.files[0]);
                    const res = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: data,
                    });
                    if (! res.ok) {
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

        if (! entry.data.url) {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/jpeg,image/png,image/webp';
            input.className = 'block w-full text-sm';

            const status = document.createElement('div');
            status.className = 'mt-2 text-xs text-neutral/50';

            input.addEventListener('change', async () => {
                if (! input.files.length) return;
                status.textContent = 'Uploading...';
                try {
                    const data = new FormData();
                    data.append('image', input.files[0]);
                    const res = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        body: data,
                    });
                    if (! res.ok) {
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
        if (marker) b.setAttribute(marker, '');
        b.className = 'rounded px-2 py-1 text-sm text-neutral/60 hover:bg-surface disabled:opacity-30';
        b.addEventListener('click', onClick);
        return b;
    }

    function addBlock(type) {
        const fresh = {
            hero:      { type: 'hero', heading: '', subtext: '', cta_text: '', cta_url: '', background_url: '' },
            rich_text: { type: 'rich_text', html: '' },
            image:     { type: 'image', url: '', alt: '', caption: '' },
            cta:       { type: 'cta', heading: '', subtext: '', button_text: '', button_url: '', background_color: '' },
            cards:     { type: 'cards', children: [] },
            contact_map:{ type: 'contact_map', heading: '', address: '', phone: '', email: '', hours: '' },
            instructors: { type: 'instructors', children: [] },
            pricing:   { type: 'pricing', children: [] },
            gallery:   { type: 'gallery', children: [] },
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
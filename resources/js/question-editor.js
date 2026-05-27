// DriveCM question editor — client-side state (D58, full re-render).
// One question at a time: type, prompt, options, one correct. D63/D64/D67.

export function createQuestionEditor(rootEl) {
    const form = rootEl.querySelector('[data-question-form]');
    const output = rootEl.querySelector('[data-question-output]');
    const typeSel = rootEl.querySelector('[data-q-type]');
    const promptEl = rootEl.querySelector('[data-q-prompt]');
    const optionsEl = rootEl.querySelector('[data-q-options]');
    const addBtn = rootEl.querySelector('[data-q-add-option]');
    const editIdEl = rootEl.querySelector('[data-q-edit-id]');
    const titleEl = rootEl.querySelector('[data-q-editor-title]');

    // State: the question being edited.
    let state = blankState();

    function blankState() {
        return {
            type: 'mcq',
            prompt: '',
            options: [{ text: '', is_correct: true }, { text: '', is_correct: false }],
        };
    }

    function render() {
        typeSel.value = state.type;
        promptEl.value = state.prompt;

        optionsEl.innerHTML = '';
        state.options.forEach((opt, i) => optionsEl.appendChild(renderOption(opt, i)));

        // D67: True/False hides add/remove; MCQ shows add (remove shown per-row if >2).
        addBtn.style.display = state.type === 'true_false' ? 'none' : '';

        sync();
    }

    function renderOption(opt, index) {
        const row = document.createElement('div');
        row.className = 'flex items-center gap-2';

        // Correct picker — radio, single-correct (D64).
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

        // Remove button — MCQ only, and only when more than 2 options (D63 floor).
        if (state.type === 'mcq' && state.options.length > 2) {
            const rm = document.createElement('button');
            rm.type = 'button';
            rm.textContent = '✕';
            rm.className = 'rounded px-2 py-1 text-sm text-red-600 hover:bg-red-50';
            rm.addEventListener('click', () => {
                state.options.splice(index, 1);
                // If we removed the correct one, default correct to the first.
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
        output.value = JSON.stringify(state);
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
            // Switching to MCQ: keep current options but ensure at least 2.
            while (state.options.length < 2) {
                state.options.push({ text: '', is_correct: false });
            }
        }
        render();
    });

    promptEl.addEventListener('input', () => { state.prompt = promptEl.value; sync(); });

    addBtn.addEventListener('click', () => {
        if (state.options.length >= 6) return; // D63 ceiling
        state.options.push({ text: '', is_correct: false });
        render();
    });

    // Edit: load an existing question into the editor.
    rootEl.querySelectorAll('[data-edit-question]').forEach((btn) => {
        btn.addEventListener('click', () => {
            state = JSON.parse(btn.dataset.editQuestion);
            editIdEl.value = btn.dataset.questionId;
            titleEl.textContent = 'Edit question';
            form.action = btn.dataset.updateUrl;
            // ensure the hidden _method=PUT field is present
            let m = form.querySelector('input[name="_method"]');
            if (!m) {
                m = document.createElement('input');
                m.type = 'hidden';
                m.name = '_method';
                form.appendChild(m);
            }
            m.value = 'PUT';
            render();
            rootEl.scrollIntoView({ behavior: 'smooth' });
        });
    });

    render();
}
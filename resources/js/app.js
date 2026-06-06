import { createBlockEditor } from './block-editor.js';
import { createPageEditor } from './page-editor.js';
import { createQuestionEditor } from './question-editor.js';

// Lesson block editor (D58)
document.querySelectorAll('[data-block-editor]').forEach((el) => {
    const initial = el.dataset.initialBlocks ? JSON.parse(el.dataset.initialBlocks) : [];
    window.__blockEditor = createBlockEditor(el, initial);
});

// Tenant page block editor (CMS-2b / D127)
document.querySelectorAll('[data-page-editor]').forEach((el) => {
    const initial = el.dataset.initialBlocks ? JSON.parse(el.dataset.initialBlocks) : [];
    window.__pageEditor = createPageEditor(el, initial);
});

// Question editor (lesson questions)
document.querySelectorAll('[data-question-editor]').forEach((el) => {
    createQuestionEditor(el);
});
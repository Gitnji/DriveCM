//
import { createBlockEditor } from './block-editor.js';

// Auto-init any block editor on the page.
document.querySelectorAll('[data-block-editor]').forEach((el) => {
    const initial = el.dataset.initialBlocks ? JSON.parse(el.dataset.initialBlocks) : [];
    window.__blockEditor = createBlockEditor(el, initial);
});
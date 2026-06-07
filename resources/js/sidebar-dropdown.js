// L2/P2 — sidebar dropdown toggle. Click parent → expand/collapse child items.
// Vanilla JS, no library. Auto-open on initial load is handled by Blade
// (the panel just doesn't have the `hidden` class when current route is a child).

document.addEventListener('click', (event) => {
    const toggle = event.target.closest('[data-nav-dropdown-toggle]');
    if (!toggle) return;

    const dropdown = toggle.closest('[data-nav-dropdown]');
    if (!dropdown) return;

    const panel = dropdown.querySelector('[data-nav-dropdown-panel]');
    const chevron = dropdown.querySelector('[data-nav-dropdown-chevron]');
    if (!panel) return;

    const isHidden = panel.classList.contains('hidden');
    if (isHidden) {
        panel.classList.remove('hidden');
        chevron?.style.setProperty('transform', 'rotate(180deg)');
    } else {
        panel.classList.add('hidden');
        chevron?.style.setProperty('transform', '');
    }
});

// On load: rotate chevron for any dropdown that's already open via `data-open`.
document.querySelectorAll('[data-nav-dropdown][data-open]').forEach((dropdown) => {
    const chevron = dropdown.querySelector('[data-nav-dropdown-chevron]');
    chevron?.style.setProperty('transform', 'rotate(180deg)');
});
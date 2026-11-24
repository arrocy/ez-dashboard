import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

window.Choices = Choices;

const dashboardShell = document.querySelector('[data-dashboard-shell]');
const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
const compactSidebarMedia = window.matchMedia('(max-width: 720px)');
const compactSidebarStorageKey = 'dashboard-sidebar-compact';

const setSidebarCompact = (isCompact, persist = true) => {
    if (!dashboardShell || !sidebarToggle) {
        return;
    }

    dashboardShell.classList.toggle('sidebar-compact', isCompact);
    sidebarToggle.setAttribute('aria-pressed', isCompact ? 'true' : 'false');
    sidebarToggle.setAttribute(
        'aria-label',
        isCompact ? 'Expand sidebar' : 'Collapse sidebar',
    );
    sidebarToggle.setAttribute('title', isCompact ? 'Expand sidebar' : 'Collapse sidebar');

    if (persist) {
        localStorage.setItem(compactSidebarStorageKey, isCompact ? 'true' : 'false');
    }
};

if (dashboardShell && sidebarToggle) {
    const savedCompactState = localStorage.getItem(compactSidebarStorageKey);
    setSidebarCompact(
        compactSidebarMedia.matches || savedCompactState === 'true',
        false,
    );

    sidebarToggle.addEventListener('click', () => {
        setSidebarCompact(!dashboardShell.classList.contains('sidebar-compact'));
    });

    compactSidebarMedia.addEventListener('change', (event) => {
        setSidebarCompact(
            event.matches || localStorage.getItem(compactSidebarStorageKey) === 'true',
            false,
        );
    });
}

document.querySelector('[data-theme-toggle]')?.addEventListener('click', () => {
    const nextTheme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-bs-theme', nextTheme);
    localStorage.setItem('dashboard-theme', nextTheme);
});

const savedTheme = localStorage.getItem('dashboard-theme');
if (savedTheme === 'dark' || savedTheme === 'light') {
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
}

document.querySelectorAll('[data-select-all]').forEach((selectAll) => {
    const form = selectAll.closest('form');

    if (!form) {
        throw new Error('The select-all checkbox must be inside a form.');
    }

    const updateBulkDeleteVisibility = () => {
        const selectedCount = form.querySelectorAll('input[name="ids[]"]:checked').length;
        const bulkDeleteButton = form.querySelector('[data-bulk-delete-button]');

        if (!bulkDeleteButton) {
            throw new Error('The bulk-delete form is missing its button.');
        }

        bulkDeleteButton.hidden = selectedCount === 0;
        selectAll.checked = selectedCount > 0
            && selectedCount === form.querySelectorAll('input[name="ids[]"]').length;
        selectAll.indeterminate = selectedCount > 0 && !selectAll.checked;
    };

    selectAll.addEventListener('change', () => {
        form.querySelectorAll(selectAll.dataset.selectAll).forEach((checkbox) => {
            checkbox.checked = selectAll.checked;
        });

        updateBulkDeleteVisibility();
    });

    form.addEventListener('change', (event) => {
        if (event.target instanceof HTMLInputElement && event.target.name === 'ids[]') {
            updateBulkDeleteVisibility();
        }
    });

    form.addEventListener('submit', (event) => {
        if (!window.confirm('Delete the selected records? This action cannot be undone.')) {
            event.preventDefault();
        }
    });

    updateBulkDeleteVisibility();
});

document.querySelectorAll('[data-delete-modal]').forEach((modal) => {
    modal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        const form = modal.querySelector('[data-delete-form]');
        const name = modal.querySelector('[data-delete-name]');

        if (!(trigger instanceof HTMLElement) || !form || !name) {
            throw new Error('The delete modal is missing its trigger, form, or name target.');
        }

        const { deleteUrl, deleteName } = trigger.dataset;
        if (!deleteUrl || deleteName === undefined) {
            throw new Error('The delete modal trigger is missing its URL or record name.');
        }

        form.action = deleteUrl;
        name.textContent = deleteName;
    });
});

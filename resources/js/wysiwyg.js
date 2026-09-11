import Quill from 'quill';
import 'quill/dist/quill.snow.css';

/**
 * Escape a plain-text string so it can be safely embedded as HTML.
 */
function escapeHtml(value) {
    const el = document.createElement('div');
    el.textContent = value;

    return el.innerHTML;
}

/**
 * Legacy posts store plain text. Quill expects HTML, so convert bare
 * newlines into paragraphs/line breaks while leaving real HTML untouched.
 */
function normalizeContent(value) {
    if (!value) {
        return '';
    }

    if (value.includes('<')) {
        return value;
    }

    return value
        .split(/\n{2,}/)
        .map((paragraph) => `<p>${escapeHtml(paragraph).replace(/\n/g, '<br>')}</p>`)
        .join('');
}

const TOOLBAR = [
    [{ header: [2, 3, false] }],
    ['bold', 'italic', 'underline', 'strike'],
    [{ list: 'ordered' }, { list: 'bullet' }],
    ['blockquote', 'code-block'],
    ['link'],
    ['clean'],
];

function registerWysiwyg() {
    const Alpine = window.Alpine;

    if (! Alpine || Alpine.__wysiwygRegistered) {
        return;
    }

    Alpine.__wysiwygRegistered = true;

    Alpine.data('wysiwyg', ({ model, placeholder = '' }) => ({
        init() {
            // The Quill instance is intentionally kept in a closure and never
            // assigned to Alpine's reactive state: a reactive Proxy wraps class
            // instances and breaks Quill's internal identity checks, throwing
            // "Cannot read properties of null (reading 'offset')".
            const quill = new Quill(this.$refs.editor, {
                theme: 'snow',
                placeholder,
                modules: { toolbar: TOOLBAR },
            });

            const initial = normalizeContent(this.$wire.get(model) ?? '');

            if (initial) {
                quill.clipboard.dangerouslyPasteHTML(initial);
            }

            quill.on('text-change', () => {
                const html = quill.getText().trim() === '' ? '' : quill.getSemanticHTML();

                this.$wire.set(model, html, false);
            });
        },
    }));
}

// Register against whichever Alpine instance Livewire boots. The name is
// resolved when an x-data expression runs, so registering late still works for
// elements initialised afterwards (e.g. wire:navigate page swaps).
if (window.Alpine) {
    registerWysiwyg();
}

document.addEventListener('alpine:init', registerWysiwyg);
document.addEventListener('livewire:init', registerWysiwyg);

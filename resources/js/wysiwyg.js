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

/**
 * Toolbar rows. The image button only appears when the editor is wired to a
 * media input, so editors without a library fall back to link-only.
 */
function toolbarRows(withImage) {
    return [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'code-block'],
        withImage ? ['link', 'image'] : ['link'],
        ['clean'],
    ];
}

function registerWysiwyg() {
    const Alpine = window.Alpine;

    if (! Alpine || Alpine.__wysiwygRegistered) {
        return;
    }

    Alpine.__wysiwygRegistered = true;

    Alpine.data('wysiwyg', ({ model, placeholder = '', mediaInputId = null }) => {
        // The Quill instance is intentionally kept in a closure and never
        // assigned to Alpine's reactive state: a reactive Proxy wraps class
        // instances and breaks Quill's internal identity checks, throwing
        // "Cannot read properties of null (reading 'offset')".
        let quill = null;

        return {
            init() {
                quill = new Quill(this.$refs.editor, {
                    theme: 'snow',
                    placeholder,
                    modules: {
                        toolbar: mediaInputId
                            ? {
                                  container: toolbarRows(true),
                                  handlers: { image: () => this.openMediaPicker() },
                              }
                            : { container: toolbarRows(false) },
                    },
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

            /**
             * The file input lives outside the editor subtree so Livewire owns
             * it; Quill's toolbar button only needs to trigger the picker.
             */
            openMediaPicker() {
                if (! mediaInputId) {
                    return;
                }

                document.getElementById(mediaInputId)?.click();
            },

            /**
             * Insert at the cursor when the editor is focused, otherwise append
             * at the end, so a gallery pick never silently lands nowhere.
             */
            insertMediaImage(url) {
                if (! quill || ! url) {
                    return;
                }

                const range = quill.getSelection(true);
                const index = range ? range.index : quill.getLength();

                quill.insertEmbed(index, 'image', url, 'user');
                quill.setSelection(index + 1, 0, 'silent');
            },
        };
    });
}

// Register against whichever Alpine instance Livewire boots. The name is
// resolved when an x-data expression runs, so registering late still works for
// elements initialised afterwards (e.g. wire:navigate page swaps).
if (window.Alpine) {
    registerWysiwyg();
}

document.addEventListener('alpine:init', registerWysiwyg);
document.addEventListener('livewire:init', registerWysiwyg);

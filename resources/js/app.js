import { animate, stagger } from 'motion';

// Aurora + hero text — runs on welcome / home pages only
function initAuroraHero() {
    const hero = document.querySelector('[data-aurora-hero]');
    if (!hero) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const blobs = hero.querySelectorAll('[data-aurora-blob]');
    const heading = hero.querySelector('[data-hero-heading]');
    const sub = hero.querySelector('[data-hero-sub]');
    const desc = hero.querySelector('[data-hero-desc]');
    const ctas = hero.querySelectorAll('[data-hero-cta]');

    // Entrance: heading → sub → desc → CTAs stagger
    if (!prefersReducedMotion) {
        const seq = [heading, sub, desc].filter(Boolean);
        if (seq.length) {
            animate(seq, { opacity: [0, 1], y: [18, 0] }, { duration: 0.7, delay: stagger(0.12), easing: [0.22, 1, 0.36, 1] });
        }
        if (ctas.length) {
            animate(ctas, { opacity: [0, 1], y: [10, 0] }, { duration: 0.6, delay: stagger(0.1, { start: 0.45 }), easing: [0.22, 1, 0.36, 1] });
        }
    }

    // Aurora blobs: slow drift + scale loop (disabled if reduced motion)
    if (prefersReducedMotion || !blobs.length) {
        return;
    }

    blobs.forEach((el, i) => {
        const dx = 18 + i * 6;
        const dur = 14 + i * 3;
        // x drift
        animate(el, { x: [0, dx, -dx * 0.6, 0] }, { duration: dur, repeat: Infinity, easing: 'ease-in-out' });
        // subtle scale breathe (offset phase)
        animate(
            el,
            { scale: [1, 1.06, 0.98, 1] },
            { duration: dur * 0.75, repeat: Infinity, easing: 'ease-in-out', delay: i * 0.9 },
        );
        // opacity pulse
        animate(el, { opacity: [0.55, 0.8, 0.5, 0.55] }, { duration: dur * 0.6, repeat: Infinity, easing: 'ease-in-out', delay: i * 0.6 });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAuroraHero);
} else {
    initAuroraHero();
}

document.addEventListener('livewire:navigated', initAuroraHero);

import { animate, stagger, scroll } from 'motion';

// Hero entrance + ikon bertebangan — runs on home page only
function initHeroAnim() {
    const hero = document.querySelector('[data-hero-anim]');
    if (!hero) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const icons = hero.querySelectorAll('[data-hero-icon]');
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

    // Sky parallax — subtle y drift on scroll (disabled if reduced motion)
    const skyImg = hero.querySelector('[data-hero-sky]');
    if (!prefersReducedMotion && skyImg && typeof scroll === 'function') {
        try {
            scroll(animate(skyImg, { y: [0, 28] }, { easing: 'linear' }), {
                target: hero,
                offset: ['start start', 'end start'],
            });
        } catch (_) {
            // Motion scroll not available in this env — ignore
        }
    }

    // Ikon bertebangan: drift naik/turun + goyang pelan (disabled if reduced motion)
    if (prefersReducedMotion || !icons.length) {
        return;
    }

    icons.forEach((el, i) => {
        const dur = 9 + i * 1.8;
        // drift lembut: naik-turun + geser kiri-kanan
        animate(
            el,
            { y: [0, -16, 0, 8, 0], x: [0, 10, -8, 0] },
            { duration: dur, repeat: Infinity, easing: 'ease-in-out', delay: i * 0.5 },
        );
        // goyang rotasi pelan (fase berbeda per ikon)
        animate(
            el,
            { rotate: [0, 5, -4, 0] },
            { duration: dur * 1.5, repeat: Infinity, easing: 'ease-in-out', delay: i * 0.5 },
        );
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroAnim);
} else {
    initHeroAnim();
}

document.addEventListener('livewire:navigated', initHeroAnim);

import { animate, stagger } from 'motion';
import './wysiwyg';

// Hero entrance — runs on home page only
function initHeroAnim() {
    const hero = document.querySelector('[data-hero-anim]');
    if (!hero) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const heading = hero.querySelector('[data-hero-heading]');
    const sub = hero.querySelector('[data-hero-sub]');
    const desc = hero.querySelector('[data-hero-desc]');
    const ctas = hero.querySelectorAll('[data-hero-cta]');

    // Entrance: heading → sub → desc → CTAs stagger
    if (prefersReducedMotion) {
        return;
    }

    const seq = [heading, sub, desc].filter(Boolean);
    if (seq.length) {
        animate(seq, { opacity: [0, 1], y: [18, 0] }, { duration: 0.7, delay: stagger(0.12), easing: [0.22, 1, 0.36, 1] });
    }
    if (ctas.length) {
        animate(ctas, { opacity: [0, 1], y: [10, 0] }, { duration: 0.6, delay: stagger(0.1, { start: 0.45 }), easing: [0.22, 1, 0.36, 1] });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProsesAnim);
} else {
    initProsesAnim();
}

document.addEventListener('livewire:navigated', initProsesAnim);

function initProsesAnim() {
    const steps = Array.from(document.querySelectorAll('[data-proses-step]'));
    if (!steps.length) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const activeClass = 'is-active';
    let index = 0;
    let dir = 1;

    const apply = () => {
        steps.forEach((el, i) => el.classList.toggle(activeClass, i === index));
    };

    // urut 1-2-3-4 lalu 4-3-2-1 lalu ulang (ping-pong)
    const tick = () => {
        const next = index + dir;
        if (next < 0) {
            dir = 1;
            index = 1;
        } else if (next >= steps.length) {
            dir = -1;
            index = steps.length - 2;
        } else {
            index = next;
        }
        apply();
    };

    // disabled jika user minta reduced motion
    if (prefersReducedMotion) {
        apply();
        return;
    }

    apply();
    let timer = setInterval(tick, 2000);

    // pause saat tab tidak terlihat, lanjut saat kembali
    document.addEventListener('visibilitychange', () => {
        clearInterval(timer);
        if (!document.hidden) {
            timer = setInterval(tick, 2000);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHeroAnim);
} else {
    initHeroAnim();
}

document.addEventListener('livewire:navigated', initHeroAnim);

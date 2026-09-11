<?php

test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

test('applies the rootly design system to the home page', function () {
    $response = $this->get(route('home'));

    $response->assertOk();

    // Brand blue primary CTA, vivid blue accent, and gradient headline (DESIGN.md).
    $response->assertSee('0a1589', false);
    $response->assertSee('43,75,255', false);
    $response->assertSee('text-gradient', false);

    // Deep-blue rounded footer ground.
    $response->assertSee('06105a', false);

    // Dark hero: deep-blue scrim over the local sky photo, white text, light nav.
    $response->assertSee('text-gradient-dark', false);
    $response->assertSee('hero-sky.jpg', false);
    $response->assertSee('data-nav-light', false);

    // Tech logos are recoloured white via CSS filters for the dark hero.
    $response->assertSee('brightness-0', false);

    // Hero tech-stack logo marquee with local brand marks.
    $response->assertSee('marquee-track', false);
    $response->assertSee('images/logos/php.svg', false);
    $response->assertSee('images/logos/nuxt.svg', false);
});

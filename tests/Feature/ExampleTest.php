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
    $response->assertSee('2b4bff', false);
    $response->assertSee('text-gradient', false);

    // Deep-blue rounded footer ground.
    $response->assertSee('06105a', false);

    // Bluish sky hero canvas (fallback ground) and sky photo background.
    $response->assertSee('b9cdff', false);
    $response->assertSee('1592207896291-e813d0419bc9', false);
});

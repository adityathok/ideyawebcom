<?php

use App\Models\Setting;

test('serves the manifest as manifest+json with the icon set for android', function () {
    $response = $this->get(route('manifest'))->assertOk();

    $response->assertHeader('Content-Type', 'application/manifest+json');

    expect($response->json('start_url'))->toBe(route('home'))
        ->and($response->json('theme_color'))->toBe('#0a1589')
        ->and($response->json('background_color'))->toBe('#ffffff')
        ->and($response->json('icons'))->toBe([
            ['src' => asset('icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => asset('icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
        ]);
});

test('uses the configured company name as the manifest name', function () {
    Setting::set('company_name', 'IdeyaWeb Studio');

    $response = $this->get(route('manifest'))->assertOk();

    expect($response->json('name'))->toBe('IdeyaWeb Studio')
        ->and($response->json('short_name'))->toBe('IdeyaWeb Studio');
});

test('falls back to the app name when no company name is configured', function () {
    config()->set('app.name', 'IdeyaWeb');

    $response = $this->get(route('manifest'))->assertOk();

    expect($response->json('name'))->toBe('IdeyaWeb');
});

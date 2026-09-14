<?php

/**
 * Favicon set: dimensi file distandarkan supaya Google (kelipatan 48px) dan iOS
 * (180x180) memakai brand mark yang benar, bukan ikon default starter kit.
 */

/** Ukuran yang tersimpan di dalam favicon.ico — dibaca dari header ICONDIR. */
function faviconIcoSizes(string $path): array
{
    $raw = (string) file_get_contents($path);
    $count = (int) (unpack('v', substr($raw, 4, 2))[1] ?? 0);
    $sizes = [];

    for ($index = 0; $index < $count; $index++) {
        $entry = unpack('Cwidth/Cheight', substr($raw, 6 + (16 * $index), 2));
        // 0 disimpan sebagai 256 sesuai spesifikasi ICO.
        $sizes[] = ($entry['width'] ?? 0) === 0 ? 256 : $entry['width'];
    }

    return $sizes;
}

test('ships a square apple touch icon at the standard 180 pixels', function () {
    $image = getimagesize(public_path('apple-touch-icon.png'));

    expect($image)->not->toBeFalse()
        ->and($image[0])->toBe(180)
        ->and($image[1])->toBe(180)
        ->and($image['mime'])->toBe('image/png');
});

test('ships the manifest icons at the sizes the manifest declares', function (string $file, int $size) {
    $image = getimagesize(public_path($file));

    expect($image)->not->toBeFalse()
        ->and($image[0])->toBe($size)
        ->and($image[1])->toBe($size)
        ->and($image['mime'])->toBe('image/png');
})->with([
    'android 192' => ['icon-192.png', 192],
    'android 512' => ['icon-512.png', 512],
]);

test('bundles a multi size favicon that includes a multiple of 48', function () {
    $sizes = faviconIcoSizes(public_path('favicon.ico'));

    expect($sizes)->toContain(16)
        ->and($sizes)->toContain(32)
        ->and($sizes)->toContain(48);
});

test('declares the icon set and the theme color in the document head', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<link rel="icon" href="'.asset('favicon.ico').'" sizes="16x16 32x32 48x48">', false)
        ->assertSee('<link rel="icon" href="'.asset('favicon.svg').'" type="image/svg+xml">', false)
        ->assertSee('<link rel="apple-touch-icon" href="'.asset('apple-touch-icon.png').'" sizes="180x180">', false)
        ->assertSee('<link rel="manifest" href="'.route('manifest').'">', false)
        ->assertSee('<meta name="theme-color" content="#0a1589">', false);
});

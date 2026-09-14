<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}

/**
 * Byte PNG yang benar-benar sah, disusun manual (tanpa GD) supaya `getimagesize()`
 * dan validasi `mimetypes` membaca isi file asli, bukan file kosong.
 */
function pngBytes(int $width = 1, int $height = 1): string
{
    $chunk = fn (string $type, string $data): string => pack('N', strlen($data)).$type.$data.pack('N', crc32($type.$data));

    $raw = '';

    for ($y = 0; $y < $height; $y++) {
        // Tiap baris diawali byte filter (0) lalu piksel RGB merah.
        $raw .= "\x00".str_repeat("\xff\x00\x00", $width);
    }

    return "\x89PNG\r\n\x1a\n"
        .$chunk('IHDR', pack('NNCCCCC', $width, $height, 8, 2, 0, 0, 0))
        .$chunk('IDAT', gzcompress($raw))
        .$chunk('IEND', '');
}

/**
 * Unggahan palsu berisi PNG yang sah, siap dipakai `StoreMediaAction`.
 */
function uploadPng(string $name = 'foto.png', int $width = 1, int $height = 1): UploadedFile
{
    return UploadedFile::fake()->createWithContent($name, pngBytes($width, $height));
}

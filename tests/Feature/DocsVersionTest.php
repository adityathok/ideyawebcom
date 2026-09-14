<?php

use App\Models\DocPage;
use App\Models\Product;

test('renders the version index with the list of published pages', function () {
    $product = Product::factory()->create(['name' => 'Aplikasi Kasir']);
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Pemasangan',
    ]);

    $this->get(route('docs.version', [$product, $version]))
        ->assertOk()
        ->assertSee('Aplikasi Kasir')
        ->assertSee($version->label)
        ->assertSee('Pemasangan')
        ->assertSee(route('docs.page', [$product, $version, $page]), false);
});

test('nests child pages under their parent in the navigation', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $parent = DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Memulai',
        'sort_order' => 0,
    ]);
    $child = DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'parent_id' => $parent->id,
        'title' => 'Instalasi',
        'sort_order' => 1,
    ]);

    $this->get(route('docs.version', [$product, $version]))
        ->assertOk()
        ->assertSee('Memulai')
        ->assertSee('Instalasi')
        ->assertSee(route('docs.page', [$product, $version, $child]), false);
});

test('keeps draft pages out of the navigation', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    DocPage::factory()->published()->create(['version_id' => $version->id, 'title' => 'Halaman Publik']);
    DocPage::factory()->draft()->create(['version_id' => $version->id, 'title' => 'Halaman Draf']);

    $this->get(route('docs.version', [$product, $version]))
        ->assertOk()
        ->assertSee('Halaman Publik')
        ->assertDontSee('Halaman Draf');
});

test('searches published pages inside a single version', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Instalasi di Linux',
        'excerpt' => 'Langkah pemasangan di Linux.',
    ]);
    DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Konfigurasi Pajak',
        'excerpt' => 'Pengaturan tarif pajak.',
    ]);

    // Ringkasan hanya tampil di daftar hasil, tidak di pohon navigasi — jadi
    // asersi ini benar-benar membuktikan hasil pencariannya tersaring.
    $this->get(route('docs.version', [$product, $version, 'q' => 'Instalasi']))
        ->assertOk()
        ->assertSee('hasil untuk')
        ->assertSee('Instalasi di Linux')
        ->assertSee('Langkah pemasangan di Linux.')
        ->assertDontSee('Pengaturan tarif pajak.');
});

test('reports an empty search result', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    DocPage::factory()->published()->create(['version_id' => $version->id, 'title' => 'Pemasangan']);

    $this->get(route('docs.version', [$product, $version, 'q' => 'kata-kunci-yang-tidak-ada']))
        ->assertOk()
        ->assertSee('Tidak ada halaman ditemukan');
});

test('returns 404 when the version belongs to another product', function () {
    $product = Product::factory()->create();
    $otherProduct = Product::factory()->create();

    // Setiap produk baru otomatis punya versi `v1`, jadi versi milik produk lain
    // sengaja diberi slug berbeda supaya yang diuji memang scoping-nya, bukan slug.
    $otherVersion = $otherProduct->versions()->firstOrFail();
    $otherVersion->update(['label' => 'v9', 'slug' => 'v9']);

    $this->get(route('docs.version', [$product, $otherVersion->fresh()]))->assertNotFound();
});

test('returns 404 for a version of an unpublished product', function () {
    $product = Product::factory()->create(['is_published' => false]);
    $version = $product->versions()->firstOrFail();

    $this->get(route('docs.version', [$product, $version]))->assertNotFound();
});

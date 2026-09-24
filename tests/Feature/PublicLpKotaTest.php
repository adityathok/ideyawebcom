<?php

use App\Models\LpKota;
use App\Models\Setting;
use App\Services\ServiceCatalog;

test('renders the city page at lp-layanan-kota with every district', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create();
    LpKota::factory()->wilayah('Klaten', 'Pedan')->create();

    $this->get('/lp-layanan-kota/surakarta')
        ->assertOk()
        ->assertSee('Jasa website')
        ->assertSee('Laweyan')
        ->assertSee('Banjarsari')
        // Kecamatan milik kota lain tidak boleh ikut tampil.
        ->assertDontSee('Pedan');
});

test('renders a district description and its own call to action', function () {
    Setting::set('phone', '08123456789');
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create(['deskripsi' => 'Pusat batik dan kuliner di barat Surakarta.']);

    $this->get(route('lp.kota', ['kota' => 'surakarta']))
        ->assertOk()
        ->assertSee('Pusat batik dan kuliner di barat Surakarta.')
        // WhatsApp per kecamatan, pesannya sudah menyebut wilayahnya.
        ->assertSee('https://wa.me/628123456789', false)
        ->assertSee('Konsultasi Laweyan');
});

test('falls back to generic copy when a district has no description', function () {
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create(['deskripsi' => null]);

    $this->get(route('lp.kota', ['kota' => 'surakarta']))
        ->assertOk()
        ->assertSee('Kami siap mengerjakan website maupun web app untuk bisnis dan instansi di Banjarsari.');
});

test('returns 404 for an unknown city slug', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kota', ['kota' => 'bandung']))->assertNotFound();
});

test('no longer serves the removed wilayah and district urls', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get('/lp')->assertNotFound();
    $this->get('/lp/surakarta')->assertNotFound();
    $this->get('/lp/surakarta/laweyan')->assertNotFound();
});

test('serves wilayah seo meta and structured data', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kota', ['kota' => 'surakarta']))
        ->assertOk()
        ->assertSee('Jasa pembuatan website, web app custom, dan WordPress untuk Laweyan — Surakarta', false)
        ->assertSee('"@type":"BreadcrumbList"', false);
});

test('lists one sitemap entry per city, without district urls', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create();
    LpKota::factory()->wilayah('Klaten', 'Pedan')->create();

    $response = $this->get(route('sitemap'))->assertOk();

    $response->assertSee(route('lp.kota', ['kota' => 'surakarta']), false)
        ->assertSee(route('lp.kota', ['kota' => 'klaten']), false);

    // Kota dengan dua kecamatan tetap satu entri, dan tidak ada URL kecamatan.
    expect(substr_count($response->getContent(), route('lp.kota', ['kota' => 'surakarta'])))->toBe(1)
        ->and($response->getContent())->not->toContain('/lp/surakarta/laweyan');
});

test('keeps the public nav readable on the dark city hero', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kota', ['kota' => 'surakarta']))
        ->assertOk()
        ->assertSee('data-nav-light="true"', false);
});

test('reuses the layanan service catalog on the city page', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $layanan = $this->get(route('layanan'))->assertOk();
    $wilayah = $this->get(route('lp.kota', ['kota' => 'surakarta']))->assertOk();

    // Judul tiap layanan harus muncul di kedua halaman, dari satu sumber.
    foreach (ServiceCatalog::all() as $service) {
        $layanan->assertSee($service['title']);
        $wilayah->assertSee($service['title']);
    }

    // Ringkasan kota tidak perlu deskripsi panjang halaman /layanan.
    $wilayah->assertDontSee(ServiceCatalog::all()[0]['desc']);
});

test('does not link wilayah pages from the public navigation', function () {
    // Halaman ini dituju langsung (iklan/SEO), bukan lewat menu situs.
    $this->get(route('home'))
        ->assertOk()
        ->assertDontSee('/lp-layanan-kota', false);
});

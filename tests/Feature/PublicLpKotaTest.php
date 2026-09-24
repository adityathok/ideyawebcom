<?php

use App\Models\LpKota;
use App\Models\Setting;
use App\Services\ServiceCatalog;

test('renders the wilayah index listing every city', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create();
    LpKota::factory()->wilayah('Klaten', 'Pedan')->create();

    $this->get(route('lp.index'))
        ->assertOk()
        ->assertSee('Wilayah Layanan')
        ->assertSee('Surakarta')
        ->assertSee('Klaten')
        ->assertSee(route('lp.kota', ['kota' => 'surakarta']), false)
        ->assertSee(route('lp.kota', ['kota' => 'klaten']), false);
});

test('renders the wilayah index with an empty state when no city exists', function () {
    $this->get(route('lp.index'))
        ->assertOk()
        ->assertSee('Belum ada wilayah terdaftar');
});

test('renders a city page with its districts', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create();
    LpKota::factory()->wilayah('Klaten', 'Pedan')->create();

    $this->get(route('lp.kota', ['kota' => 'surakarta']))
        ->assertOk()
        ->assertSee('Laweyan')
        ->assertSee('Banjarsari')
        ->assertSee(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'laweyan']), false)
        // Kecamatan milik kota lain tidak boleh ikut tampil.
        ->assertDontSee('Pedan');
});

test('renders a district page with description and sibling links', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create(['deskripsi' => 'Pusat batik dan kuliner di barat Surakarta.']);
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create();

    $this->get(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'laweyan']))
        ->assertOk()
        ->assertSee('Laweyan')
        ->assertSee('Pusat batik dan kuliner di barat Surakarta.')
        // Sidebar menautkan kecamatan lain di kota yang sama.
        ->assertSee(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'banjarsari']), false);
});

test('returns 404 for unknown city and district slugs', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kota', ['kota' => 'bandung']))->assertNotFound();
    $this->get(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'bandung']))->assertNotFound();
    $this->get(route('lp.kecamatan', ['kota' => 'bandung', 'kecamatan' => 'laweyan']))->assertNotFound();
});

test('serves wilayah seo meta and structured data', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'laweyan']))
        ->assertOk()
        ->assertSee('Jasa pembuatan website, web app custom, dan WordPress untuk Laweyan — Surakarta', false)
        ->assertSee('"@type":"BreadcrumbList"', false);
});

test('shows a whatsapp call to action when the profile phone is set', function () {
    Setting::set('phone', '08123456789');
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'laweyan']))
        ->assertOk()
        ->assertSee('https://wa.me/628123456789', false);
});

test('lists wilayah urls in the sitemap', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();
    LpKota::factory()->wilayah('Surakarta', 'Banjarsari')->create();

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('lp.kota', ['kota' => 'surakarta']), false)
        ->assertSee(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'laweyan']), false)
        ->assertSee(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'banjarsari']), false);
});

test('keeps the public nav readable on a dark wilayah hero', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $this->get(route('lp.kota', ['kota' => 'surakarta']))
        ->assertOk()
        ->assertSee('data-nav-light="true"', false);
});

test('reuses the layanan service catalog on district pages', function () {
    LpKota::factory()->wilayah('Surakarta', 'Laweyan')->create();

    $layanan = $this->get(route('layanan'))->assertOk();
    $wilayah = $this->get(route('lp.kecamatan', ['kota' => 'surakarta', 'kecamatan' => 'laweyan']))->assertOk();

    // Judul tiap layanan harus muncul di kedua halaman, dari satu sumber.
    foreach (ServiceCatalog::all() as $service) {
        $layanan->assertSee($service['title']);
        $wilayah->assertSee($service['title']);
    }

    // Ringkasan wilayah tidak perlu deskripsi panjang halaman /layanan.
    $wilayah->assertDontSee(ServiceCatalog::all()[0]['desc']);
});

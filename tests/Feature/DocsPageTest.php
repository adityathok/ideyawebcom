<?php

use App\Models\DocPage;
use App\Models\Product;

test('renders a published page with its markdown body and table of contents', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Pemasangan',
        'body' => "## Persyaratan\n\nButuh PHP 8.3.\n\n## Langkah\n\n1. Unduh\n2. Pasang",
    ]);

    $this->get(route('docs.page', [$product, $version, $page]))
        ->assertOk()
        ->assertSee('<h2 id="persyaratan">', false)
        ->assertSee('<h2 id="langkah">', false)
        ->assertSee('Butuh PHP 8.3.')
        ->assertSee('Di halaman ini')
        ->assertSee('href="#langkah"', false);
});

test('renders the feedback widget with the aggregate', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    $this->get(route('docs.page', [$product, $version, $page]))
        ->assertOk()
        ->assertSee('Apakah halaman ini membantu?');
});

test('counts a page view without touching the update timestamp', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id, 'view_count' => 0]);

    $before = $page->fresh();

    $this->get(route('docs.page', [$product, $version, $page]))->assertOk();

    $after = $page->fresh();

    expect($after->view_count)->toBe(1)
        ->and($after->updated_at?->timestamp)->toBe($before->updated_at?->timestamp);
});

test('links to the previous and next page in navigation order', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $first = DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Halaman Satu',
        'sort_order' => 0,
    ]);
    $second = DocPage::factory()->published()->create([
        'version_id' => $version->id,
        'title' => 'Halaman Dua',
        'sort_order' => 1,
    ]);

    $this->get(route('docs.page', [$product, $version, $first]))
        ->assertOk()
        ->assertSee('Berikutnya →')
        ->assertDontSee('← Sebelumnya');

    $this->get(route('docs.page', [$product, $version, $second]))
        ->assertOk()
        ->assertSee('← Sebelumnya')
        ->assertDontSee('Berikutnya →');
});

test('returns 404 for a draft page', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->draft()->create(['version_id' => $version->id]);

    $this->get(route('docs.page', [$product, $version, $page]))->assertNotFound();
});

test('returns 404 when the page belongs to another version', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();

    $otherProduct = Product::factory()->create();
    $otherVersion = $otherProduct->versions()->firstOrFail();
    $otherPage = DocPage::factory()->published()->create(['version_id' => $otherVersion->id]);

    $this->get(route('docs.page', [$product, $version, $otherPage]))->assertNotFound();
});

test('returns 404 for a page of an unpublished product', function () {
    $product = Product::factory()->create(['is_published' => false]);
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    $this->get(route('docs.page', [$product, $version, $page]))->assertNotFound();
});

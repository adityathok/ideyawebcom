<?php

use App\Models\DocPage;
use App\Models\Product;

test('renders the docs index with published products', function () {
    $product = Product::factory()->create(['name' => 'Aplikasi Kasir', 'slug' => 'aplikasi-kasir']);
    DocPage::factory()->published()->create([
        'version_id' => $product->versions()->firstOrFail()->id,
        'title' => 'Pemasangan',
    ]);

    $this->get(route('docs.index'))
        ->assertOk()
        ->assertSee('Dokumentasi')
        ->assertSee('Aplikasi Kasir')
        ->assertSee('1 halaman')
        ->assertSee(route('docs.product', $product), false);
});

test('lists every version of a published product', function () {
    $product = Product::factory()->create(['name' => 'Aplikasi Kasir']);
    $version = $product->versions()->firstOrFail();
    $version->update(['label' => 'v1.0']);

    $this->get(route('docs.index'))
        ->assertOk()
        ->assertSee('v1.0');
});

test('hides products that are not published', function () {
    Product::factory()->create(['name' => 'Produk Rahasia', 'is_published' => false]);

    $this->get(route('docs.index'))
        ->assertOk()
        ->assertDontSee('Produk Rahasia');
});

test('renders the docs index empty state', function () {
    $this->get(route('docs.index'))
        ->assertOk()
        ->assertSee('Belum ada dokumentasi');
});

test('links to the docs from the public header navigation', function () {
    // The menu item is exposed across public pages, inactive off the docs.
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('docs.index'), false)
        ->assertSee('hover:text-[#100f12]">Dokumentasi</a>', false);

    // Docs pages highlight it as the active item.
    $this->get(route('docs.index'))
        ->assertOk()
        ->assertSee('bg-[#f3f6ff] text-[#0a1589]">Dokumentasi</a>', false);
});

test('redirects a product url to its current version', function () {
    $product = Product::factory()->create();
    $current = $product->versions()->firstOrFail();

    $this->get(route('docs.product', $product))
        ->assertRedirect(route('docs.version', [$product, $current]));
});

test('redirects to the version marked as current', function () {
    $product = Product::factory()->create();
    $first = $product->versions()->firstOrFail();
    $second = $product->versions()->create([
        'label' => 'v2',
        'slug' => 'v2',
        'is_current' => true,
        'sort_order' => 1,
    ]);
    $first->update(['is_current' => false]);

    $this->get(route('docs.product', $product))
        ->assertRedirect(route('docs.version', [$product, $second]));
});

test('returns 404 for a product url that is not published', function () {
    $product = Product::factory()->create(['is_published' => false]);

    $this->get(route('docs.product', $product))->assertNotFound();
});

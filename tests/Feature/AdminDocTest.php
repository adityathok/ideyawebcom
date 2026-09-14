<?php

use App\Enums\DocStatus;
use App\Models\DocPage;
use App\Models\DocVersion;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('the documentation admin pages render before any data exists', function () {
    Livewire::test('pages::admin.products.index')->assertOk();
    Livewire::test('pages::admin.versions.index')->assertOk();
    Livewire::test('pages::admin.docs.index')->assertOk();
    Livewire::test('pages::admin.doc-form.index')->assertOk();
});

test('a product can be created from the modal and gets a default version', function () {
    Livewire::test('pages::admin.products.index')
        ->call('create')
        ->set('name', 'Aplikasi Kasir')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'product-form');

    $product = Product::where('name', 'Aplikasi Kasir')->firstOrFail();

    // Hook `created()` model menyiapkan versi `v1` supaya /docs/{produk} selalu punya tujuan.
    expect($product->slug)->toBe('aplikasi-kasir')
        ->and($product->versions()->count())->toBe(1)
        ->and($product->versions()->firstOrFail()->is_current)->toBeTrue();
});

test('editing a product loads it into the modal', function () {
    $product = Product::factory()->create(['name' => 'Aplikasi Kasir', 'slug' => 'aplikasi-kasir']);

    Livewire::test('pages::admin.products.index')
        ->call('edit', $product->id)
        ->assertSet('editingId', $product->id)
        ->assertSet('name', 'Aplikasi Kasir')
        ->assertDispatched('modal-show', name: 'product-form');
});

test('deleting a product requires confirmation', function () {
    $product = Product::factory()->create();

    $component = Livewire::test('pages::admin.products.index')
        ->call('confirmDelete', $product->id)
        ->assertSet('deletingId', $product->id)
        ->assertDispatched('modal-show', name: 'confirm-product-deletion');

    expect(Product::find($product->id))->not->toBeNull();

    $component->call('delete')->assertHasNoErrors();

    expect(Product::find($product->id))->toBeNull();
});

test('saving a version as current demotes the other versions of that product', function () {
    $product = Product::factory()->create();
    $first = $product->versions()->firstOrFail();
    $second = $product->versions()->create([
        'label' => 'v2',
        'slug' => 'v2',
        'is_current' => false,
        'sort_order' => 1,
    ]);

    Livewire::test('pages::admin.versions.index')
        ->call('edit', $second->id)
        ->set('is_current', true)
        ->call('save')
        ->assertHasNoErrors();

    expect($second->fresh()->is_current)->toBeTrue()
        ->and($first->fresh()->is_current)->toBeFalse();
});

test('a version slug must be unique inside its product', function () {
    $product = Product::factory()->create();

    Livewire::test('pages::admin.versions.index')
        ->call('create')
        ->set('product_id', $product->id)
        ->set('label', 'v1')
        ->call('save')
        ->assertHasErrors('slug');

    expect(DocVersion::where('product_id', $product->id)->count())->toBe(1);
});

test('the only version of a product cannot be deleted', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();

    Livewire::test('pages::admin.versions.index')
        ->call('confirmDelete', $version->id)
        ->assertSet('deletingBlocked', true)
        ->call('delete');

    expect(DocVersion::find($version->id))->not->toBeNull();
});

test('the version list can be filtered by product', function () {
    $alfa = Product::factory()->create(['name' => 'Produk Alfa']);
    $beta = Product::factory()->create(['name' => 'Produk Beta']);

    // Label versi, bukan nama produk: nama produk juga muncul di pilihan filter.
    $alfa->versions()->firstOrFail()->update(['label' => 'v9.9']);
    $beta->versions()->firstOrFail()->update(['label' => 'beta-1']);

    Livewire::test('pages::admin.versions.index', ['filterProduct' => (string) $alfa->id])
        ->assertSee('v9.9')
        ->assertDontSee('beta-1');
});

test('the doc list can be filtered by version', function () {
    $product = Product::factory()->create();
    $first = $product->versions()->firstOrFail();
    $second = $product->versions()->create(['label' => 'v2', 'slug' => 'v2', 'sort_order' => 1]);

    DocPage::factory()->create(['version_id' => $first->id, 'title' => 'Halaman Versi Satu']);
    DocPage::factory()->create(['version_id' => $second->id, 'title' => 'Halaman Versi Dua']);

    Livewire::test('pages::admin.docs.index', ['filterVersion' => (string) $second->id])
        ->assertSee('Halaman Versi Dua')
        ->assertDontSee('Halaman Versi Satu');
});

test('a doc page can be moved up among its siblings', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $first = DocPage::factory()->create(['version_id' => $version->id, 'title' => 'Satu', 'sort_order' => 0]);
    $second = DocPage::factory()->create(['version_id' => $version->id, 'title' => 'Dua', 'sort_order' => 1]);

    Livewire::test('pages::admin.docs.index')
        ->call('moveUp', $second->id)
        ->assertHasNoErrors();

    expect($second->fresh()->sort_order)->toBe(0)
        ->and($first->fresh()->sort_order)->toBe(1);

    // Yang sudah paling atas tidak bergerak, dan urutannya tetap rapat 0..n-1.
    Livewire::test('pages::admin.docs.index')->call('moveUp', $second->id);

    expect($second->fresh()->sort_order)->toBe(0)
        ->and($first->fresh()->sort_order)->toBe(1);
});

test('reordering does not touch the update timestamp', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    DocPage::factory()->create(['version_id' => $version->id, 'sort_order' => 0]);
    $second = DocPage::factory()->create(['version_id' => $version->id, 'sort_order' => 1]);

    $before = $second->fresh()->updated_at;

    Livewire::test('pages::admin.docs.index')->call('moveUp', $second->id);

    expect($second->fresh()->updated_at?->timestamp)->toBe($before?->timestamp);
});

test('deleting a doc page frees its slug for a new page', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->create([
        'version_id' => $version->id,
        'title' => 'Pemasangan',
        'slug' => 'pemasangan',
    ]);

    Livewire::test('pages::admin.docs.index')
        ->call('delete', $page->id)
        ->assertHasNoErrors();

    expect(DocPage::find($page->id))->toBeNull();

    // Index unik `(version_id, slug)` mencakup baris terhapus, jadi slug lama harus lepas.
    Livewire::test('pages::admin.doc-form.index')
        ->set('version_id', $version->id)
        ->set('title', 'Pemasangan')
        ->set('slug', 'pemasangan')
        ->set('body', 'Konten baru.')
        ->call('save')
        ->assertHasNoErrors();

    expect(DocPage::where('version_id', $version->id)->where('slug', 'pemasangan')->exists())->toBeTrue();
});

test('creating a doc page derives the product from the version', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();

    Livewire::test('pages::admin.doc-form.index')
        ->set('version_id', $version->id)
        ->set('title', 'Pemasangan')
        ->set('body', 'Konten halaman.')
        ->call('save')
        ->assertHasNoErrors();

    $page = DocPage::where('version_id', $version->id)->where('slug', 'pemasangan')->firstOrFail();

    expect($page->product_id)->toBe($product->id)
        ->and($page->excerpt)->not->toBeNull();
});

test('saving a doc page stays on the form', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->create(['version_id' => $version->id, 'title' => 'Judul Awal']);

    Livewire::test('pages::admin.doc-form.index', ['id' => $page->id])
        ->assertSet('isEdit', true)
        ->assertSet('version_id', $version->id)
        ->set('title', 'Judul Diperbarui')
        ->call('save')
        ->assertNoRedirect()
        ->assertSet('isEdit', true)
        ->assertSet('id', $page->id);

    expect($page->fresh()->title)->toBe('Judul Diperbarui');
});

test('publishing a doc page fills the published date', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();

    Livewire::test('pages::admin.doc-form.index')
        ->set('version_id', $version->id)
        ->set('title', 'Halaman Baru')
        ->set('body', 'Konten halaman.')
        ->set('status', 'published')
        ->call('save')
        ->assertHasNoErrors();

    $page = DocPage::where('slug', 'halaman-baru')->firstOrFail();

    expect($page->status)->toBe(DocStatus::Published)
        ->and($page->published_at)->not->toBeNull();
});

test('a parent page from another version is rejected', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();

    $otherProduct = Product::factory()->create();
    $otherVersion = $otherProduct->versions()->firstOrFail();
    $otherParent = DocPage::factory()->create(['version_id' => $otherVersion->id]);

    Livewire::test('pages::admin.doc-form.index')
        ->set('version_id', $version->id)
        ->set('parent_id', $otherParent->id)
        ->set('title', 'Halaman Anak')
        ->set('body', 'Konten halaman.')
        ->call('save')
        ->assertHasErrors('parent_id');

    expect(DocPage::where('version_id', $version->id)->count())->toBe(0);
});

test('a doc page cannot become its own parent', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->create(['version_id' => $version->id]);

    Livewire::test('pages::admin.doc-form.index', ['id' => $page->id])
        ->set('parent_id', $page->id)
        ->call('save')
        ->assertHasErrors('parent_id');

    expect($page->fresh()->parent_id)->toBeNull();
});

test('the markdown preview renders the body', function () {
    Livewire::test('pages::admin.doc-form.index')
        ->set('body', '## Judul Bagian')
        ->call('togglePreview')
        ->assertSet('showPreview', true)
        ->assertSee('<h2 id="judul-bagian">', false);
});

<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LpKotaController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\WebManifestController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/layanan', [PageController::class, 'services'])->name('layanan');
Route::get('/kontak-kami', [PageController::class, 'contact'])->name('kontak');
Route::post('/kontak-kami', [PageController::class, 'sendContact'])->name('kontak.send');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/site.webmanifest', WebManifestController::class)->name('manifest');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/kategori/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');

// Landing page per kota. Kecamatan tidak punya URL sendiri; isinya masuk ke
// halaman kota. Parameter binding memakai nama kelas (`{lpKota}`) supaya rute
// admin `admin/lp-kota` yang sudah ada tidak berubah.
Route::get('/lp-layanan-kota/{kota}', [LpKotaController::class, 'kota'])->name('lp.kota');

// `scopeBindings()` mengunci {version} lewat Product::versions() dan {page} lewat
// DocVersion::pages(), sehingga versi maupun halaman milik produk lain tidak bisa
// diakses lewat URL produk ini.
Route::get('/docs', [DocsController::class, 'index'])->name('docs.index');
Route::get('/docs/{product:slug}', [DocsController::class, 'product'])->name('docs.product');
Route::get('/docs/{product:slug}/{version:slug}', [DocsController::class, 'version'])->name('docs.version')->scopeBindings();
Route::get('/docs/{product:slug}/{version:slug}/{page:slug}', [DocsController::class, 'page'])->name('docs.page')->scopeBindings();

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('admin/posts', 'pages::admin.posts.index')->name('admin.posts');
    Route::livewire('admin/post-form', 'pages::admin.post-form.index')->name('admin.post-form');
    Route::livewire('admin/categories', 'pages::admin.categories.index')->name('admin.categories');
    Route::livewire('admin/tags', 'pages::admin.tags.index')->name('admin.tags');
    Route::livewire('admin/users', 'pages::admin.users.index')->name('admin.users');
    Route::livewire('admin/settings', 'pages::admin.settings.index')->name('admin.settings');
    Route::livewire('admin/docs', 'pages::admin.docs.index')->name('admin.docs');
    Route::livewire('admin/doc-form', 'pages::admin.doc-form.index')->name('admin.doc-form');
    Route::livewire('admin/products', 'pages::admin.products.index')->name('admin.products');
    Route::livewire('admin/versions', 'pages::admin.versions.index')->name('admin.versions');
    Route::livewire('admin/media', 'pages::admin.media.index')->name('admin.media');
    Route::livewire('admin/lp-kota', 'pages::admin.lp-kota.index')->name('admin.lp-kota');
});

Route::redirect('admin/profile', '/admin/settings')->name('admin.profile');

require __DIR__.'/settings.php';

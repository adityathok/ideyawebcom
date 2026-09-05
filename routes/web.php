<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/kategori/{category:slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{tag:slug}', [BlogController::class, 'tag'])->name('blog.tag');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('admin/posts', 'pages::admin.posts.index')->name('admin.posts');
    Route::livewire('admin/post-form', 'pages::admin.post-form.index')->name('admin.post-form');
    Route::livewire('admin/categories', 'pages::admin.categories.index')->name('admin.categories');
    Route::livewire('admin/tags', 'pages::admin.tags.index')->name('admin.tags');
    Route::livewire('admin/settings', 'pages::admin.settings.index')->name('admin.settings');
});

Route::redirect('admin/profile', '/admin/settings')->name('admin.profile');

require __DIR__.'/settings.php';

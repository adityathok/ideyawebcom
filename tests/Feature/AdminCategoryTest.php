<?php

use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('category list renders the delete action', function () {
    Category::factory()->create(['name' => 'Berita', 'slug' => 'berita']);

    Livewire::test('pages::admin.categories.index')
        ->assertSee('Berita')
        ->assertSee('Hapus');
});

test('deleting a category requires confirmation through the modal', function () {
    $category = Category::factory()->create(['name' => 'Berita', 'slug' => 'berita']);

    $component = Livewire::test('pages::admin.categories.index')
        ->call('confirmDelete', $category->id)
        ->assertSet('deletingId', $category->id)
        ->assertSet('deletingName', 'Berita')
        ->assertDispatched('modal-show', name: 'confirm-category-deletion');

    expect(Category::find($category->id))->not->toBeNull();

    $component->call('delete')
        ->assertSet('deletingId', null)
        ->assertDispatched('modal-close', name: 'confirm-category-deletion');

    expect(Category::find($category->id))->toBeNull();
});

test('the category form opens as a modal when creating', function () {
    Livewire::test('pages::admin.categories.index')
        ->call('create')
        ->assertDispatched('modal-show', name: 'category-form');
});

test('the category form opens as a modal when editing', function () {
    $category = Category::factory()->create(['name' => 'Berita', 'slug' => 'berita']);

    Livewire::test('pages::admin.categories.index')
        ->call('edit', $category->id)
        ->assertSet('editingId', $category->id)
        ->assertDispatched('modal-show', name: 'category-form');
});

test('a category can be saved from the modal', function () {
    Livewire::test('pages::admin.categories.index')
        ->call('create')
        ->set('name', 'Berita')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'category-form');

    expect(Category::where('slug', 'berita')->exists())->toBeTrue();
});

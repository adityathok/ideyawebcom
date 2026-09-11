<?php

use App\Models\Tag;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('tag list renders edit and delete actions', function () {
    Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

    Livewire::test('pages::admin.tags.index')
        ->assertSee('Laravel')
        ->assertSee('Edit')
        ->assertSee('Hapus');
});

test('a tag can be created', function () {
    Livewire::test('pages::admin.tags.index')
        ->call('create')
        ->assertSet('showForm', true)
        ->set('name', 'Laravel')
        ->call('save')
        ->assertHasNoErrors();

    expect(Tag::where('slug', 'laravel')->exists())->toBeTrue();
});

test('editing a tag loads its values into the form', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

    Livewire::test('pages::admin.tags.index')
        ->call('edit', $tag->id)
        ->assertSet('editingId', $tag->id)
        ->assertSet('name', 'Laravel')
        ->assertSet('slug', 'laravel')
        ->assertSet('showForm', true);
});

test('a tag can be updated while keeping its slug', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']);

    Livewire::test('pages::admin.tags.index')
        ->call('edit', $tag->id)
        ->set('name', 'Laravel Framework')
        ->call('save')
        ->assertHasNoErrors();

    expect($tag->fresh()->name)->toBe('Laravel Framework');
    expect($tag->fresh()->slug)->toBe('laravel');
});

test('deleting a tag requires confirmation through the modal', function () {
    $tag = Tag::factory()->create(['name' => 'Laravel']);

    $component = Livewire::test('pages::admin.tags.index')
        ->call('confirmDelete', $tag->id)
        ->assertSet('deletingId', $tag->id)
        ->assertSet('deletingName', 'Laravel')
        ->assertDispatched('modal-show', name: 'confirm-tag-deletion');

    expect(Tag::find($tag->id))->not->toBeNull();

    $component->call('delete')
        ->assertSet('deletingId', null)
        ->assertDispatched('modal-close', name: 'confirm-tag-deletion');

    expect(Tag::find($tag->id))->toBeNull();
});

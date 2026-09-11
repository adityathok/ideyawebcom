<?php

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('creating a post stores tags from a comma separated input', function () {
    Livewire::test('pages::admin.post-form.index')
        ->set('title', 'Judul Post')
        ->set('body', 'Isi post.')
        ->set('tags', 'Laravel, livewire , php')
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('title', 'Judul Post')->firstOrFail();

    expect($post->tags->pluck('name')->sort()->values()->all())
        ->toBe(['Laravel', 'livewire', 'php']);
});

test('editing a post loads its tags into the input', function () {
    $post = Post::factory()->create();
    $post->tags()->attach(Tag::factory()->create(['name' => 'Laravel', 'slug' => 'laravel']));

    Livewire::test('pages::admin.post-form.index', ['id' => $post->id])
        ->assertSet('tags', 'Laravel');
});

test('tags differing only in case are stored once', function () {
    Livewire::test('pages::admin.post-form.index')
        ->set('title', 'Judul Post')
        ->set('body', 'Isi post.')
        ->set('tags', 'Laravel, laravel, LARAVEL')
        ->call('save')
        ->assertHasNoErrors();

    expect(Tag::where('slug', 'laravel')->count())->toBe(1);
});

test('saving a post stays on the form instead of redirecting', function () {
    $post = Post::factory()->published()->create();

    Livewire::test('pages::admin.post-form.index', ['id' => $post->id])
        ->set('title', 'Judul Diperbarui')
        ->call('save')
        ->assertNoRedirect()
        ->assertSet('isEdit', true)
        ->assertSet('id', $post->id)
        ->assertSet('slug', $post->fresh()->slug);
});

test('editing a post loads its html body', function () {
    $post = Post::factory()->create(['body' => '<p>Halo <strong>dunia</strong></p>']);

    Livewire::test('pages::admin.post-form.index', ['id' => $post->id])
        ->assertSet('body', '<p>Halo <strong>dunia</strong></p>');
});

test('saving a post stores the html body and strips it for the excerpt', function () {
    Livewire::test('pages::admin.post-form.index')
        ->set('title', 'Judul Post')
        ->set('body', '<p>Halo <strong>dunia</strong></p>')
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('title', 'Judul Post')->firstOrFail();

    expect($post->body)->toBe('<p>Halo <strong>dunia</strong></p>');
    expect($post->excerpt)->toBe('Halo dunia');
});

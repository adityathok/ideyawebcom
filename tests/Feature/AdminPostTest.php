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

<?php

use App\Models\Media;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');

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

test('uploading a cover puts it in the media library and attaches it to the post', function () {
    Livewire::test('pages::admin.post-form.index')
        ->set('title', 'Judul Post')
        ->set('body', 'Isi post.')
        ->set('coverUpload', uploadPng('cover.png', 8, 6))
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('title', 'Judul Post')->firstOrFail();
    $media = Media::sole();

    expect($post->coverMedia()?->id)->toBe($media->id)
        ->and($post->image)->toBeNull()
        ->and($post->imageUrl())->toBe($media->url());

    Storage::disk('public')->assertExists($media->path);
});

test('picking media from the library attaches it as the post cover', function () {
    $media = Media::factory()->withFile()->create();

    Livewire::test('pages::admin.post-form.index')
        ->set('title', 'Judul Post')
        ->set('body', 'Isi post.')
        ->call('openMediaPicker', 'cover')
        ->assertSet('pickerTarget', 'cover')
        ->assertDispatched('modal-show', name: 'media-picker')
        ->call('selectMedia', $media->id)
        ->assertSet('coverMediaId', $media->id)
        ->call('save')
        ->assertHasNoErrors();

    $post = Post::where('title', 'Judul Post')->firstOrFail();

    expect($post->coverMedia()?->id)->toBe($media->id);
});

test('the post editor exposes a media upload input for inline images', function () {
    Livewire::test('pages::admin.post-form.index')
        ->assertSeeHtml('id="wysiwyg-media-body"')
        ->assertSeeHtml('wire:model="inlineImageUpload"');
});

test('picking media for the body dispatches the insert event for the editor', function () {
    $media = Media::factory()->withFile()->create();

    Livewire::test('pages::admin.post-form.index')
        ->call('openMediaPicker', 'inline')
        ->assertSet('pickerTarget', 'inline')
        ->call('selectMedia', $media->id)
        ->assertDispatched('media-image-inserted', url: $media->url())
        ->assertDispatched('modal-close', name: 'media-picker')
        ->assertSet('coverMediaId', null);
});

test('removing the cover detaches the media and clears the legacy image columns', function () {
    $post = Post::factory()->create(['image' => 'posts/lama.jpg', 'image_caption' => 'Caption lama']);
    $post->setCoverMedia(Media::factory()->withFile()->create());

    Livewire::test('pages::admin.post-form.index', ['id' => $post->id])
        ->assertSet('coverMediaId', $post->coverMedia()?->id)
        ->call('removeImage')
        ->assertSet('coverMediaId', null)
        ->assertSet('existingImage', null);

    $fresh = $post->fresh();

    expect($fresh->coverMedia())->toBeNull()
        ->and($fresh->image)->toBeNull()
        ->and($fresh->image_caption)->toBeNull()
        ->and($fresh->imageUrl())->toBeNull();
});

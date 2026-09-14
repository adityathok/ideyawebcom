<?php

use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    Storage::fake('public');

    $this->actingAs(User::factory()->create());
});

test('the media library shows an empty state when no media exists', function () {
    Livewire::test('pages::admin.media.index')
        ->assertOk()
        ->assertSee('Belum ada media');
});

test('uploading a file stores it in the library', function () {
    Livewire::test('pages::admin.media.index')
        ->set('uploads', [uploadPng('foto.png', 4, 3)])
        ->assertHasNoErrors()
        ->assertSet('uploads', []);

    $media = Media::sole();

    expect($media->original_name)->toBe('foto.png')
        ->and($media->uploaded_by)->toBe(auth()->id());

    Storage::disk('public')->assertExists($media->path);
});

test('uploading a file that is not an allowed image is rejected', function () {
    Livewire::test('pages::admin.media.index')
        ->set('uploads', [UploadedFile::fake()->createWithContent('catatan.txt', 'halo')])
        ->assertHasErrors('uploads.0');

    expect(Media::count())->toBe(0);
});

test('alt text and caption can be saved from the detail modal', function () {
    $media = Media::factory()->create(['alt_text' => null, 'caption' => null]);

    Livewire::test('pages::admin.media.index')
        ->call('edit', $media->id)
        ->assertSet('editingId', $media->id)
        ->set('alt_text', 'Tangkapan layar dasbor')
        ->set('caption', 'Dasbor utama aplikasi.')
        ->call('saveDetails')
        ->assertHasNoErrors()
        ->assertDispatched('modal-close', name: 'media-detail');

    $fresh = $media->fresh();

    expect($fresh->alt_text)->toBe('Tangkapan layar dasbor')
        ->and($fresh->caption)->toBe('Dasbor utama aplikasi.');
});

test('media that is not used anywhere can be deleted', function () {
    $media = Media::factory()->create();

    Livewire::test('pages::admin.media.index')
        ->call('confirmDelete', $media->id)
        ->assertSet('deleteBlocked', false)
        ->call('delete')
        ->assertSet('deletingId', null)
        ->assertDispatched('modal-close', name: 'confirm-media-deletion');

    expect(Media::find($media->id))->toBeNull();
});

test('media that is still used by a post cannot be deleted', function () {
    $media = Media::factory()->create();
    $post = Post::factory()->create();
    $post->setCoverMedia($media);

    Livewire::test('pages::admin.media.index')
        ->call('confirmDelete', $media->id)
        ->assertSet('deleteBlocked', true)
        ->assertSet('deletingUsage', 1)
        ->call('delete')
        ->assertSet('deleteBlocked', true);

    expect(Media::find($media->id))->not->toBeNull();
});

test('searching filters the library by file name, alt text, or caption', function () {
    Media::factory()->create(['original_name' => 'logo-perusahaan.png']);
    $match = Media::factory()->create(['original_name' => 'foto-tim.jpg', 'alt_text' => 'Tim kami']);

    Livewire::test('pages::admin.media.index')
        ->set('search', 'Tim kami')
        ->assertSee('foto-tim.jpg')
        ->assertDontSee('logo-perusahaan.png');

    expect($match->fresh())->not->toBeNull();
});

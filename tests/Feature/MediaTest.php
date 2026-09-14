<?php

use App\Actions\Media\DeleteMediaAction;
use App\Actions\Media\StoreMediaAction;
use App\Models\DocPage;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

beforeEach(function (): void {
    Storage::fake('public');
});

test('media url points at the file on its disk', function () {
    $media = Media::factory()->create(['disk' => 'public', 'path' => 'media/2026/09/foto.png']);

    expect($media->url())->toBe(Storage::disk('public')->url('media/2026/09/foto.png'));
});

test('media size is shown in a readable unit', function () {
    expect(Media::factory()->create(['size' => 512])->humanSize())->toBe('512 B')
        ->and(Media::factory()->create(['size' => 2048])->humanSize())->toBe('2 KB')
        ->and(Media::factory()->create(['size' => 1536 * 1024])->humanSize())->toBe('1,5 MB');
});

test('storing an upload writes the file and records its metadata', function () {
    $user = User::factory()->create();

    $media = app(StoreMediaAction::class)->handle(uploadPng('foto.png', 4, 3), $user->id);

    expect($media->original_name)->toBe('foto.png')
        ->and($media->mime_type)->toBe('image/png')
        ->and($media->extension)->toBe('png')
        ->and($media->width)->toBe(4)
        ->and($media->height)->toBe(3)
        ->and($media->size)->toBeGreaterThan(0)
        ->and($media->uploaded_by)->toBe($user->id);

    Storage::disk('public')->assertExists($media->path);
});

test('storing identical content reuses the existing media row', function () {
    $action = app(StoreMediaAction::class);

    $first = $action->handle(uploadPng('satu.png', 2, 2));
    $second = $action->handle(uploadPng('dua.png', 2, 2));

    expect($second->id)->toBe($first->id)
        ->and(Media::count())->toBe(1);
});

test('the upload rules accept allowed images and reject other file types', function () {
    $allowed = Validator::make(['file' => uploadPng()], ['file' => StoreMediaAction::validationRules(true)]);
    $rejected = Validator::make(
        ['file' => UploadedFile::fake()->createWithContent('catatan.txt', 'halo')],
        ['file' => StoreMediaAction::validationRules(true)]
    );

    expect($allowed->passes())->toBeTrue()
        ->and($rejected->passes())->toBeFalse();
});

test('a post cover comes from the attached media', function () {
    $media = Media::factory()->create();
    $post = Post::factory()->create();

    $post->setCoverMedia($media);

    $fresh = $post->fresh();

    expect($fresh->coverMedia()?->id)->toBe($media->id)
        ->and($fresh->coverUrl())->toBe($media->url())
        ->and($fresh->imageUrl())->toBe($media->url());
});

test('a post without cover media still falls back to the legacy image column', function () {
    $post = Post::factory()->create(['image' => 'posts/lama.jpg']);

    expect($post->imageUrl())->toBe(Storage::disk('public')->url('posts/lama.jpg'));
});

test('inline images in a post body are tracked as media usage', function () {
    $media = Media::factory()->create();
    $post = Post::factory()->create();

    $post->update(['body' => '<p>teks</p><img src="'.$media->url().'" alt="">']);

    expect($media->fresh()->isInUse())->toBeTrue();

    $post->update(['body' => '<p>tanpa gambar</p>']);

    expect($media->fresh()->isInUse())->toBeFalse();
});

test('inline images in a doc page body are tracked as media usage', function () {
    $media = Media::factory()->create();
    $page = DocPage::factory()->create();

    $page->update(['body' => "# Judul\n\n![contoh](".$media->url().")\n"]);

    expect($media->fresh()->isInUse())->toBeTrue();
});

test('syncing inline media leaves the cover attachment alone', function () {
    $cover = Media::factory()->create();
    $post = Post::factory()->create();
    $post->setCoverMedia($cover);

    $post->update(['body' => '<p>tanpa gambar sama sekali</p>']);

    expect($post->fresh()->coverMedia()?->id)->toBe($cover->id);
});

test('media that is still used cannot be deleted', function () {
    $media = Media::factory()->create();
    $post = Post::factory()->create();
    $post->setCoverMedia($media);

    expect(app(DeleteMediaAction::class)->handle($media->fresh()))->toBeFalse()
        ->and(Media::find($media->id))->not->toBeNull();
});

test('unused media can be deleted', function () {
    $media = Media::factory()->create();

    expect(app(DeleteMediaAction::class)->handle($media))->toBeTrue()
        ->and(Media::find($media->id))->toBeNull();
});

test('force deleting media removes the file from disk', function () {
    $media = Media::factory()->withFile()->create();

    Storage::disk('public')->assertExists($media->path);

    $media->forceDelete();

    Storage::disk('public')->assertMissing($media->path);
});

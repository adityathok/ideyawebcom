<?php

declare(strict_types=1);

use App\Models\Media;
use App\Models\Post;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Pindahkan path gambar post lama (kolom `posts.image`) ke perpustakaan media
     * supaya gambar yang sudah ada ikut terkelola dan tercatat pemakaiannya.
     *
     * Post yang filenya sudah hilang dari disk sengaja dilewati — `Post::imageUrl()`
     * masih membaca kolom lama sebagai cadangan.
     */
    public function up(): void
    {
        $disk = (string) config('media.disk', 'public');

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk($disk);

        $morphType = (new Post)->getMorphClass();

        DB::table('posts')
            ->whereNotNull('image')
            ->where('image', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($posts) use ($disk, $storage, $morphType): void {
                foreach ($posts as $post) {
                    $path = (string) $post->image;

                    if ($path === '' || ! $storage->exists($path)) {
                        continue;
                    }

                    $dimensions = $this->dimensions($storage, $path);

                    $media = Media::withTrashed()->firstOrCreate(
                        ['disk' => $disk, 'path' => $path],
                        [
                            'original_name' => basename($path),
                            'mime_type' => $storage->mimeType($path) ?: 'image/jpeg',
                            'extension' => pathinfo($path, PATHINFO_EXTENSION) ?: null,
                            'size' => (int) ($storage->size($path) ?: 0),
                            'width' => $dimensions[0],
                            'height' => $dimensions[1],
                            'caption' => $post->image_caption,
                        ]
                    );

                    DB::table('mediables')->insertOrIgnore([
                        'media_id' => $media->id,
                        'mediable_type' => $morphType,
                        'mediable_id' => $post->id,
                        'collection' => Media::COLLECTION_COVER,
                        'sort_order' => 0,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        DB::table('mediables')
            ->where('mediable_type', (new Post)->getMorphClass())
            ->where('collection', Media::COLLECTION_COVER)
            ->delete();

        DB::table('media')
            ->whereNotIn('id', DB::table('mediables')->select('media_id'))
            ->delete();
    }

    /**
     * Dimensi hanya bisa dibaca dari disk lokal; driver remote mengembalikan null.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function dimensions(FilesystemAdapter $storage, string $path): array
    {
        $absolute = $storage->path($path);

        if (! is_file($absolute)) {
            return [null, null];
        }

        $size = @getimagesize($absolute);

        return $size === false ? [null, null] : [(int) $size[0], (int) $size[1]];
    }
};

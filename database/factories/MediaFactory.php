<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @extends Factory<Media>
 */
final class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $name = fake()->word().'.jpg';

        return [
            'disk' => (string) config('media.disk', 'public'),
            'path' => 'media/'.now()->format('Y/m').'/'.Str::ulid()->toBase32().'.jpg',
            'original_name' => $name,
            'mime_type' => 'image/jpeg',
            'extension' => 'jpg',
            'size' => fake()->numberBetween(1024, 512000),
            'width' => 1200,
            'height' => 800,
            'hash' => hash('sha1', $name.Str::random(16)),
            'alt_text' => null,
            'caption' => null,
            'uploaded_by' => null,
        ];
    }

    /**
     * Tulis file betulan ke disk supaya tes bisa memeriksa siklus hapus file.
     */
    public function withFile(): static
    {
        return $this->afterCreating(function (Media $media): void {
            Storage::disk($media->disk)->put($media->path, 'fake-media-bytes');
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Actions\Media;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Satu-satunya jalan masuk file ke perpustakaan media: validasi tipe, nama file
 * yang aman, dan deduplikasi isi file.
 */
final class StoreMediaAction
{
    /**
     * @return list<string>
     */
    public static function allowedMimes(): array
    {
        /** @var array<string, string> $mimes */
        $mimes = (array) config('media.allowed_mimes', []);

        return array_keys($mimes);
    }

    /**
     * Aturan validasi untuk properti Livewire berisi file unggahan.
     *
     * `mimetypes:` dipakai (bukan `mimes:`) supaya tipe ditentukan dari isi file.
     *
     * @return array<int, string>
     */
    public static function validationRules(bool $required = false): array
    {
        return [
            $required ? 'required' : 'nullable',
            'file',
            'max:'.(int) config('media.max_size_kb', 4096),
            'mimetypes:'.implode(',', self::allowedMimes()),
        ];
    }

    public static function extensionFor(string $mimeType): ?string
    {
        /** @var array<string, string> $mimes */
        $mimes = (array) config('media.allowed_mimes', []);

        return $mimes[$mimeType] ?? null;
    }

    public function handle(UploadedFile $file, ?int $uploadedBy = null): Media
    {
        $disk = (string) config('media.disk', 'public');
        $absolutePath = $file->getRealPath();

        if ($absolutePath === false) {
            throw new RuntimeException('File unggahan tidak bisa dibaca dari disk sementara.');
        }

        $hash = hash_file('sha1', $absolutePath);
        $hash = $hash === false ? null : $hash;

        // Unggahan identik dipakai ulang: satu file di disk, banyak pemakaian.
        $existing = $hash === null
            ? null
            : Media::query()->where('disk', $disk)->where('hash', $hash)->first();

        if ($existing !== null && Storage::disk($disk)->exists($existing->path)) {
            return $existing;
        }

        $mimeType = (string) ($file->getMimeType() ?: 'application/octet-stream');
        $extension = self::extensionFor($mimeType) ?? strtolower($file->getClientOriginalExtension() ?: 'bin');
        $directory = trim((string) config('media.directory', 'media'), '/').'/'.now()->format('Y/m');

        // Nama file dibuat sendiri (ulid), bukan dari nama asli: menghindari
        // tabrakan nama sekaligus menutup celah path traversal.
        $path = $file->storeAs($directory, Str::ulid()->toBase32().'.'.$extension, [
            'disk' => $disk,
            'visibility' => 'public',
        ]);

        if ($path === false) {
            throw new RuntimeException("Gagal menyimpan file media ke disk {$disk}.");
        }

        [$width, $height] = self::dimensions($absolutePath, $mimeType);

        return Media::create([
            'disk' => $disk,
            'path' => $path,
            'original_name' => basename($file->getClientOriginalName()),
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => (int) $file->getSize(),
            'width' => $width,
            'height' => $height,
            'hash' => $hash,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    /**
     * @return array{0: int|null, 1: int|null}
     */
    private static function dimensions(string $absolutePath, string $mimeType): array
    {
        if (! str_starts_with($mimeType, 'image/')) {
            return [null, null];
        }

        $size = @getimagesize($absolutePath);

        return $size === false ? [null, null] : [(int) $size[0], (int) $size[1]];
    }
}

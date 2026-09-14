<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Satu baris mewakili satu file di disk — sumber tunggal untuk semua media yang
 * diunggah dan dipakai post maupun halaman dokumentasi.
 *
 * @property int $id
 * @property string $disk
 * @property string $path
 * @property string $original_name
 * @property string $mime_type
 * @property string|null $extension
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property string|null $hash
 * @property string|null $alt_text
 * @property string|null $caption
 * @property int|null $uploaded_by
 * @property-read int|null $attachments_count
 */
#[Fillable(['disk', 'path', 'original_name', 'mime_type', 'extension', 'size', 'width', 'height', 'hash', 'alt_text', 'caption', 'uploaded_by'])]
final class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory, SoftDeletes;

    /** Media yang dipakai sebagai gambar utama satu record. */
    public const string COLLECTION_COVER = 'cover';

    /** Media yang muncul di dalam body (HTML post atau Markdown halaman docs). */
    public const string COLLECTION_INLINE = 'inline';

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'width' => 'integer',
            'height' => 'integer',
            'uploaded_by' => 'integer',
        ];
    }

    /** @return BelongsTo<User, $this> */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /** @return HasMany<MediaAttachment, $this> */
    public function attachments(): HasMany
    {
        return $this->hasMany(MediaAttachment::class);
    }

    public function url(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Ukuran file dalam satuan yang mudah dibaca.
     *
     * Ditulis manual, bukan `Number::fileSize()`, karena method bawaan itu butuh
     * ekstensi `intl` yang belum tentu terpasang di server.
     */
    public function humanSize(): string
    {
        $bytes = max(0, $this->size);
        $units = ['B', 'KB', 'MB', 'GB'];

        $power = 0;

        while ($bytes >= 1024 && $power < count($units) - 1) {
            $bytes /= 1024;
            $power++;
        }

        $rounded = $power === 0
            ? (string) (int) $bytes
            : rtrim(rtrim(number_format($bytes, 1, ',', '.'), '0'), ',');

        return $rounded.' '.$units[$power];
    }

    /**
     * Media yang masih terpasang di post atau halaman docs tidak boleh dihapus:
     * baris pivot-nya adalah satu-satunya cara tahu file itu masih dipakai.
     */
    public function isInUse(): bool
    {
        return $this->attachments()->exists();
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeImages(Builder $q): Builder
    {
        return $q->where('mime_type', 'like', 'image/%');
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $q->when($term, fn (Builder $qq) => $qq->where(fn (Builder $i) => $i->where('original_name', 'like', "%{$term}%")->orWhere('alt_text', 'like', "%{$term}%")->orWhere('caption', 'like', "%{$term}%")));
    }

    protected static function booted(): void
    {
        // File hanya dilepas saat force delete. Soft delete sengaja menyisakan
        // file karena media bisa dipulihkan atau masih dipakai record lain.
        self::forceDeleting(function (self $media): void {
            Storage::disk($media->disk)->delete($media->path);
        });
    }
}

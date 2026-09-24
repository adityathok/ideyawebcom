<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use Database\Factories\LpKotaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

/**
 * Landing page per wilayah: satu baris mewakili satu kecamatan di satu kota,
 * lengkap dengan gambar utama (hero) dan ikon wilayah.
 *
 * @property int $id
 * @property string $nama_kota
 * @property string $nama_kecamatan
 * @property string|null $deskripsi
 * @property string|null $gambar_utama
 * @property string|null $gambar_icon
 */
#[Fillable(['nama_kota', 'nama_kecamatan', 'deskripsi', 'gambar_utama', 'gambar_icon'])]
final class LpKota extends Model
{
    /** @use HasFactory<LpKotaFactory> */
    use HasFactory, HasMedia;

    protected $table = 'lp_kota';

    /** Koleksi media untuk gambar hero wilayah. */
    public const string COLLECTION_GAMBAR_UTAMA = 'gambar_utama';

    /** Koleksi media untuk ikon wilayah. */
    public const string COLLECTION_GAMBAR_ICON = 'gambar_icon';

    /** @return MorphToMany<Media, $this> */
    public function gambarUtamaMedia(): MorphToMany
    {
        return $this->media()->wherePivot('collection', self::COLLECTION_GAMBAR_UTAMA);
    }

    /** @return MorphToMany<Media, $this> */
    public function gambarIconMedia(): MorphToMany
    {
        return $this->media()->wherePivot('collection', self::COLLECTION_GAMBAR_ICON);
    }

    /**
     * URL gambar utama: perpustakaan media dulu, kolom lama sebagai cadangan
     * supaya data yang belum ikut migrasi tetap tampil.
     */
    public function gambarUtamaUrl(): ?string
    {
        return $this->collectionMediaUrl(self::COLLECTION_GAMBAR_UTAMA, $this->gambar_utama);
    }

    public function gambarIconUrl(): ?string
    {
        return $this->collectionMediaUrl(self::COLLECTION_GAMBAR_ICON, $this->gambar_icon);
    }

    /**
     * Ambil gambar satu koleksi, memakai relasi yang sudah di-eager-load kalau
     * ada supaya daftar di admin tidak menembak satu query per baris.
     */
    private function collectionMediaUrl(string $collection, ?string $fallbackPath): ?string
    {
        $media = $this->relationLoaded('media')
            ? $this->media->firstWhere('pivot.collection', $collection)
            : $this->media()->wherePivot('collection', $collection)->first();

        return $media?->url() ?? $this->storageUrl($fallbackPath);
    }

    /**
     * Pasang gambar dari perpustakaan media. `null` berarti koleksi itu
     * dikosongkan; koleksi lain tidak tersentuh.
     */
    public function setGambarUtama(?Media $media): void
    {
        $this->setMediaForCollection(self::COLLECTION_GAMBAR_UTAMA, $media);
    }

    public function setGambarIcon(?Media $media): void
    {
        $this->setMediaForCollection(self::COLLECTION_GAMBAR_ICON, $media);
    }

    public function labelWilayah(): string
    {
        return $this->nama_kecamatan.', '.$this->nama_kota;
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $q->when($term, fn (Builder $qq) => $qq->where(fn (Builder $i) => $i
            ->where('nama_kota', 'like', "%{$term}%")
            ->orWhere('nama_kecamatan', 'like', "%{$term}%")
            ->orWhere('deskripsi', 'like', "%{$term}%")));
    }

    private function storageUrl(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        return $disk->url($path);
    }
}

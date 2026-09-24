<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Models\Media;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Storage;

/**
 * Menghubungkan model (post, halaman docs) ke perpustakaan media.
 *
 * Dua jenis pemakaian dibedakan lewat `collection` di tabel `mediables`:
 * `cover` dipilih eksplisit di form, sedangkan `inline` disinkronkan dari isi
 * body supaya gambar yang dihapus dari editor tidak meninggalkan catatan basi.
 */
trait HasMedia
{
    /** @return MorphToMany<Media, $this> */
    public function media(): MorphToMany
    {
        return $this->morphToMany(Media::class, 'mediable')
            ->withPivot(['collection', 'sort_order'])
            ->withTimestamps();
    }

    /** @return MorphToMany<Media, $this> */
    public function cover(): MorphToMany
    {
        return $this->media()->wherePivot('collection', Media::COLLECTION_COVER);
    }

    public function coverMedia(): ?Media
    {
        return $this->cover->first();
    }

    public function coverUrl(): ?string
    {
        return $this->coverMedia()?->url();
    }

    public function attachMedia(Media $media, string $collection = Media::COLLECTION_INLINE): void
    {
        $this->media()->syncWithoutDetaching([
            $media->getKey() => ['collection' => $collection],
        ]);
    }

    public function setCoverMedia(?Media $media): void
    {
        $this->cover()->detach();

        if ($media !== null) {
            $this->attachMedia($media, Media::COLLECTION_COVER);
        }

        $this->unsetRelation('cover');
        $this->unsetRelation('media');
    }

    /**
     * Sinkronkan gambar milik satu koleksi: `null` menghapus, model menambah.
     *
     * Koleksi lain dibiarkan utuh, jadi model dengan beberapa gambar (misalnya
     * LP kota: gambar utama + ikon) tidak saling menghapus saat salah satu
     * diperbarui. Koleksi `inline` tetap dikelola `syncInlineMedia()`.
     */
    public function setMediaForCollection(string $collection, ?Media $media): void
    {
        $this->media()->wherePivot('collection', $collection)->detach();

        if ($media !== null) {
            $this->attachMedia($media, $collection);
        }

        $this->unsetRelation('media');
    }

    /**
     * Selaraskan catatan pemakaian gambar inline dengan isi body terbaru.
     *
     * Referensi dikenali dari URL-nya (bukan dari id), karena body bisa berupa
     * HTML dari editor post maupun Markdown halaman docs, dan keduanya menyimpan
     * URL hasil `Media::url()`. Koleksi `cover` tidak disentuh.
     */
    public function syncInlineMedia(): void
    {
        $disk = (string) config('media.disk', 'public');
        $paths = $this->referencedMediaPaths((string) ($this->body ?? ''), $disk);

        $referenced = $paths === []
            ? []
            : Media::query()->where('disk', $disk)->whereIn('path', $paths)->pluck('id')->map(fn (mixed $id): int => (int) $id)->all();

        $attached = $this->media()
            ->wherePivot('collection', Media::COLLECTION_INLINE)
            ->pluck('media.id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();

        $detach = array_diff($attached, $referenced);

        if ($detach !== []) {
            $this->media()->wherePivot('collection', Media::COLLECTION_INLINE)->detach($detach);
        }

        foreach (array_diff($referenced, $attached) as $id) {
            $this->media()->attach($id, ['collection' => Media::COLLECTION_INLINE]);
        }

        $this->unsetRelation('media');
    }

    /**
     * @return list<string>
     */
    private function referencedMediaPaths(string $body, string $disk): array
    {
        $base = rtrim(Storage::disk($disk)->url(''), '/');

        if ($base === '' || $body === '') {
            return [];
        }

        preg_match_all('#'.preg_quote($base, '#').'/([^\s"\'<>()\[\]]+)#i', $body, $matches);

        $paths = [];

        foreach ($matches[1] as $candidate) {
            $path = ltrim(rawurldecode($candidate), '/');

            if ($path !== '') {
                $paths[$path] = true;
            }
        }

        return array_keys($paths);
    }

    protected static function bootHasMedia(): void
    {
        // Dijalankan tiap kali body disimpan, apa pun jalur simpannya (form admin,
        // factory, atau seeder), sehingga invariant "pivot = isi body" tidak drift.
        self::saved(function (self $model): void {
            $model->syncInlineMedia();
        });
    }
}

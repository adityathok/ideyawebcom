<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Baris pivot `mediables`: catatan satu pemakaian media oleh satu record.
 *
 * Dipakai untuk menjawab "media ini dipakai di mana" sekaligus menahan media
 * yang masih terpakai agar tidak ikut terhapus.
 *
 * @property int $id
 * @property int $media_id
 * @property string $mediable_type
 * @property int $mediable_id
 * @property string $collection
 * @property int $sort_order
 */
#[Fillable(['media_id', 'mediable_type', 'mediable_id', 'collection', 'sort_order'])]
final class MediaAttachment extends Model
{
    protected $table = 'mediables';

    protected function casts(): array
    {
        return [
            'media_id' => 'integer',
            'mediable_id' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    /** @return BelongsTo<Media, $this> */
    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }

    /** @return MorphTo<Model, $this> */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }
}

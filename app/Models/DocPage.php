<?php

declare(strict_types=1);

namespace App\Models;

use App\Concerns\HasMedia;
use App\Enums\DocStatus;
use Carbon\CarbonImmutable;
use Database\Factories\DocPageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $product_id
 * @property int $version_id
 * @property int|null $parent_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property DocStatus $status
 * @property int $sort_order
 * @property CarbonImmutable|null $published_at
 * @property int $view_count
 */
#[Fillable(['version_id', 'parent_id', 'title', 'slug', 'excerpt', 'body', 'status', 'sort_order', 'published_at', 'view_count'])]
final class DocPage extends Model
{
    /** @use HasFactory<DocPageFactory> */
    use HasFactory, HasMedia, SoftDeletes;

    protected function casts(): array
    {
        return ['status' => DocStatus::class, 'published_at' => 'immutable_datetime', 'parent_id' => 'integer', 'sort_order' => 'integer', 'view_count' => 'integer'];
    }

    /** @return BelongsTo<DocVersion, $this> */
    public function version(): BelongsTo
    {
        return $this->belongsTo(DocVersion::class);
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return BelongsTo<self, $this> */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /** @return HasMany<self, $this> */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->ordered();
    }

    /** @return HasMany<DocFeedback, $this> */
    public function feedback(): HasMany
    {
        return $this->hasMany(DocFeedback::class, 'doc_page_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', DocStatus::Published);
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('title');
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $q->when($term, fn (Builder $qq) => $qq->where(fn (Builder $i) => $i->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%")->orWhere('body', 'like', "%{$term}%")));
    }

    protected static function booted(): void
    {
        // `product_id` didenormalisasi dari versi supaya invariant tidak bisa drift,
        // apa pun jalur pembuatan halamannya (form admin, factory, atau seeder).
        self::saving(function (self $p): void {
            if (blank($p->version_id)) {
                return;
            }

            $productId = DocVersion::whereKey($p->version_id)->value('product_id');

            if ($productId !== null) {
                $p->product_id = (int) $productId;
            }
        });

        self::creating(function (self $p): void {
            if (empty($p->slug)) {
                $p->slug = Str::slug($p->title);
            } if (empty($p->excerpt) && ! empty($p->body)) {
                $p->excerpt = Str::limit(strip_tags($p->body), 160);
            }
        });

        // Unique index `(version_id, slug)` mencakup baris yang di-soft delete,
        // sehingga slug harus dilepas saat dihapus — kalau tidak, halaman baru
        // dengan judul sama akan menabrak index dan gagal disimpan.
        self::deleting(function (self $p): void {
            $p->slug = Str::limit($p->slug, 220, '').'-deleted-'.$p->id;
            $p->saveQuietly();
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\DocVersionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int $product_id
 * @property string $label
 * @property string $slug
 * @property bool $is_current
 * @property int $sort_order
 */
#[Fillable(['product_id', 'label', 'slug', 'is_current', 'sort_order', 'released_at'])]
final class DocVersion extends Model
{
    /** @use HasFactory<DocVersionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_current' => 'boolean', 'sort_order' => 'integer', 'released_at' => 'date'];
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** @return HasMany<DocPage, $this> */
    public function pages(): HasMany
    {
        return $this->hasMany(DocPage::class, 'version_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('label');
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeCurrent(Builder $q): Builder
    {
        return $q->where('is_current', true);
    }

    protected static function booted(): void
    {
        self::creating(function (self $v): void {
            if (empty($v->slug)) {
                $v->slug = Str::slug($v->label);
            }
        });
    }
}

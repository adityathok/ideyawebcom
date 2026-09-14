<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $tagline
 * @property string|null $description
 * @property string|null $logo
 * @property int $sort_order
 * @property bool $is_published
 */
#[Fillable(['name', 'slug', 'tagline', 'description', 'logo', 'color', 'website_url', 'sort_order', 'is_published'])]
final class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_published' => 'boolean', 'sort_order' => 'integer'];
    }

    /** @return HasMany<DocVersion, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(DocVersion::class);
    }

    /** @return HasManyThrough<DocPage, DocVersion, $this> */
    public function pages(): HasManyThrough
    {
        return $this->hasManyThrough(DocPage::class, DocVersion::class, 'product_id', 'version_id');
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
        return $q->where('is_published', true);
    }

    /**
     * @param  Builder<self>  $q
     * @return Builder<self>
     */
    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->orderBy('name');
    }

    public function logoUrl(): ?string
    {
        if (blank($this->logo)) {
            return null;
        }

        if (str_starts_with($this->logo, 'http://') || str_starts_with($this->logo, 'https://') || str_starts_with($this->logo, '/')) {
            return $this->logo;
        }

        return Storage::disk('public')->url($this->logo);
    }

    protected static function booted(): void
    {
        self::creating(function (self $p): void {
            if (empty($p->slug)) {
                $p->slug = Str::slug($p->name);
            }
        });

        // Setiap produk wajib punya minimal satu versi, supaya `/docs/{produk}`
        // selalu punya tujuan redirect yang valid.
        self::created(function (self $p): void {
            $p->versions()->create([
                'label' => 'v1',
                'slug' => 'v1',
                'is_current' => true,
                'sort_order' => 0,
            ]);
        });
    }
}

<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** @property int $id @property string $title @property PostStatus $status */
#[Fillable(['title', 'slug', 'excerpt', 'body', 'cover_image', 'image', 'image_caption', 'status', 'category_id', 'user_id', 'published_at', 'view_count'])]
final class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, SoftDeletes;

    protected function casts(): array
    {
        return ['status' => PostStatus::class, 'published_at' => 'immutable_datetime', 'view_count' => 'integer'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @param Builder<self> $q */
    public function scopePublished(Builder $q): Builder
    {
        return $q->where('status', PostStatus::Published);
    }

    /** @param Builder<self> $q */
    public function scopeSearch(Builder $q, ?string $term): Builder
    {
        return $q->when($term, fn (Builder $qq) => $qq->where(fn (Builder $i) => $i->where('title', 'like', "%{$term}%")->orWhere('excerpt', 'like', "%{$term}%")->orWhere('body', 'like', "%{$term}%")));
    }

    public function imageUrl(): ?string
    {
        if (filled($this->image)) {
            return Storage::disk('public')->url($this->image);
        }

        if (filled($this->cover_image)) {
            if (str_starts_with($this->cover_image, 'http://') || str_starts_with($this->cover_image, 'https://') || str_starts_with($this->cover_image, '/')) {
                return $this->cover_image;
            }

            return Storage::disk('public')->url($this->cover_image);
        }

        return null;
    }

    protected static function booted(): void
    {
        self::creating(function (self $p): void {
            if (empty($p->slug)) {
                $p->slug = Str::slug($p->title).'-'.Str::lower(Str::random(6));
            } if (empty($p->excerpt) && ! empty($p->body)) {
                $p->excerpt = Str::limit(strip_tags($p->body), 160);
            }
        });
    }
}

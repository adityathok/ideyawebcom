<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

/** @property int $id @property string $name @property string $slug */
#[Fillable(['name', 'slug'])]
final class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        self::creating(function (self $t): void {
            if (empty($t->slug)) {
                $t->slug = Str::slug($t->name);
            }
        });
    }
}

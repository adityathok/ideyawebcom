<?php

declare(strict_types=1);

namespace App\Enums;

enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft', self::Published => 'Published', self::Archived => 'Archived'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'zinc', self::Published => 'green', self::Archived => 'amber'
        };
    }

    /** @return string[] */
    public static function values(): array
    {
        return array_map(fn (self $c) => $c->value, self::cases());
    }
}

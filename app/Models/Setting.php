<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

final class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group'];

    protected $casts = [
        'value' => 'string',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::rememberForever("setting.{$key}", function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            if (! $setting) {
                return null;
            }

            return match ($setting->type) {
                'json' => $setting->value ? json_decode($setting->value, true) : $default,
                'boolean' => $setting->value === '1' || $setting->value === 'true',
                'integer' => (int) $setting->value,
                default => $setting->value,
            };
        }) ?? $default;
    }

    public static function set(string $key, mixed $value, string $type = 'string', string $group = 'general'): self
    {
        $stored = match ($type) {
            'json' => json_encode($value),
            'boolean' => $value ? '1' : '0',
            default => (string) $value,
        };

        $setting = self::updateOrCreate(['key' => $key], [
            'value' => $stored,
            'type' => $type,
            'group' => $group,
        ]);

        Cache::forget("setting.{$key}");

        return $setting;
    }

    public static function getGroup(string $group): array
    {
        return self::where('group', $group)->pluck('value', 'key')->toArray();
    }

    /** @return array<string, string> */
    public static function profile(): array
    {
        $keys = ['company_name', 'tagline', 'about', 'email', 'phone', 'address', 'logo', 'facebook', 'instagram', 'twitter', 'linkedin'];
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = (string) (self::get($key, '') ?? '');
        }

        return $result;
    }

    /** @return array<string, string> */
    public static function seo(): array
    {
        $keys = ['seo_title', 'seo_description', 'seo_keywords', 'seo_og_image'];
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = (string) (self::get($key, '') ?? '');
        }

        return $result;
    }

    public static function seoOgImageUrl(): ?string
    {
        $path = (string) (self::get('seo_og_image', '') ?? '');
        if ($path === '') {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}

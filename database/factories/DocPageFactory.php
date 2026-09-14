<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocStatus;
use App\Models\DocPage;
use App\Models\DocVersion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocPage>
 */
final class DocPageFactory extends Factory
{
    protected $model = DocPage::class;

    public function definition(): array
    {
        $title = rtrim(fake()->sentence(mt_rand(3, 7)), '.');

        return [
            // `product_id` sengaja tidak diisi: hook saving() menurunkannya dari versi.
            'version_id' => DocVersion::factory(),
            'parent_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'excerpt' => fake()->sentence(16),
            'body' => fake()->paragraphs(mt_rand(3, 6), true),
            'status' => DocStatus::Draft,
            'sort_order' => fake()->numberBetween(0, 20),
            'published_at' => null,
            'view_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => DocStatus::Published,
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => DocStatus::Draft,
            'published_at' => null,
        ]);
    }
}

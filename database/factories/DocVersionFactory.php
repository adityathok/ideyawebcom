<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DocVersion;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocVersion>
 */
final class DocVersionFactory extends Factory
{
    protected $model = DocVersion::class;

    public function definition(): array
    {
        $label = 'v'.fake()->numberBetween(1, 9).'.'.fake()->numberBetween(0, 12);

        return [
            'product_id' => Product::factory(),
            'label' => $label,
            'slug' => Str::slug($label).'-'.Str::lower(Str::random(6)),
            'is_current' => false,
            'sort_order' => fake()->numberBetween(0, 10),
            'released_at' => fake()->dateTimeBetween('-2 years', 'now'),
        ];
    }

    public function current(): static
    {
        return $this->state(fn (array $a): array => [
            'is_current' => true,
        ]);
    }
}

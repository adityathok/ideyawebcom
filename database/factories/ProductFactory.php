<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
final class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        /** @var array<int, string> $words */
        $words = fake()->words(mt_rand(1, 3));
        $name = rtrim(implode(' ', $words), '.');

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(6)),
            'tagline' => fake()->sentence(6),
            'description' => fake()->paragraphs(mt_rand(2, 4), true),
            'logo' => null,
            'color' => fake()->randomElement(['indigo', 'emerald', 'rose', 'amber', 'sky']),
            'website_url' => 'https://example.com',
            'sort_order' => fake()->numberBetween(0, 20),
            'is_published' => true,
        ];
    }
}

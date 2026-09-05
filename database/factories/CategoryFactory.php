<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Category> */ final class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $n = fake()->unique()->words(mt_rand(1, 3), true);

        return ['name' => Str::title($n), 'slug' => Str::slug($n).'-'.Str::lower(Str::random(4)), 'description' => fake()->sentence(12), 'color' => fake()->hexColor()];
    }
}

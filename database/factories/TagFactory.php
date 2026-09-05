<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Tag> */ final class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        $n = fake()->unique()->word();

        return ['name' => Str::title($n), 'slug' => Str::slug($n).'-'.Str::lower(Str::random(3))];
    }
}

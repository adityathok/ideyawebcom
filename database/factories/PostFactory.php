<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
final class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = rtrim(fake()->sentence(mt_rand(4, 8)), '.');

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.Str::lower(Str::random(6)),
            'excerpt' => fake()->sentence(16),
            'body' => fake()->paragraphs(mt_rand(4, 8), true),
            'cover_image' => null,
            'image' => null,
            'image_caption' => null,
            'status' => PostStatus::Draft,
            'published_at' => null,
            'view_count' => fake()->numberBetween(0, 500),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => PostStatus::Published,
            'published_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $a): array => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }
}

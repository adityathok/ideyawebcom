<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

final class PostSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $users = collect([User::factory()->create(['email' => 'admin@example.com'])]);
        } $tags = Tag::all();
        $cats = Category::all();
        Post::factory(15)->published()->recycle($users)->recycle($cats)->create()->each(fn (Post $p) => $p->tags()->attach($tags->random(mt_rand(2, 4))->pluck('id')));
        Post::factory(5)->draft()->recycle($users)->recycle($cats)->create()->each(fn (Post $p) => $p->tags()->attach($tags->random(mt_rand(1, 3))->pluck('id')));
    }
}

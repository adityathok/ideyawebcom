<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class TagSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Laravel', 'Livewire', 'Flux', 'PHP', 'JavaScript', 'Tailwind', 'Alpine.js', 'MySQL', 'API', 'SEO', 'UI/UX', 'Startup'] as $n) {
            Tag::firstOrCreate(['slug' => Str::slug($n)], ['name' => $n, 'slug' => Str::slug($n)]);
        }
    }
}

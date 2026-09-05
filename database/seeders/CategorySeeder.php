<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $cats = [['name' => 'Teknologi', 'color' => '#3B82F6', 'description' => 'Teknologi & inovasi'], ['name' => 'Bisnis', 'color' => '#10B981', 'description' => 'Insight bisnis'], ['name' => 'Desain', 'color' => '#F59E0B', 'description' => 'UI/UX & branding'], ['name' => 'Marketing', 'color' => '#EF4444', 'description' => 'Growth'], ['name' => 'Tutorial', 'color' => '#8B5CF6', 'description' => 'Panduan'], ['name' => 'Berita', 'color' => '#06B6D4', 'description' => 'Update']];
        foreach ($cats as $d) {
            Category::firstOrCreate(['slug' => Str::slug($d['name'])], $d + ['slug' => Str::slug($d['name'])]);
        }
    }
}

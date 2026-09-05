<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'admin', 'password' => bcrypt('password')]
        );

        $this->call([
            CategorySeeder::class,
            TagSeeder::class,
            SettingSeeder::class,
            PostSeeder::class,
        ]);
    }
}

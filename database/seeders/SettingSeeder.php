<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

final class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'company_name' => 'IdeyaWeb',
            'tagline' => 'Developer Website & Web App',
            'about' => 'IdeyaWeb adalah developer website, web app, dan app — spesialis membangun web app & app custom, dan berpengalaman mengerjakan WordPress, dari pembuatan hingga optimasi dan perawatan.',
            'email' => 'hello@ideyaweb.com',
            'phone' => '+62 812-3456-7890',
            'address' => 'Jl. Teknologi No.123, Jakarta Selatan',
            'logo' => '',
            'facebook' => 'https://facebook.com/ideyawebcom',
            'instagram' => 'https://instagram.com/ideyawebcom',
            'twitter' => 'https://x.com/ideyawebcom',
            'linkedin' => 'https://linkedin.com/company/ideyawebcom',
        ];

        foreach ($defaults as $key => $value) {
            Setting::firstOrCreate(['key' => $key], [
                'value' => $value,
                'type' => 'string',
                'group' => 'profile',
            ]);
        }
    }
}

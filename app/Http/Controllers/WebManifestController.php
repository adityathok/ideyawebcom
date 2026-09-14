<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\JsonResponse;

/**
 * Web app manifest — sumber ikon resolusi tinggi (192/512) untuk crawler dan ikon
 * "Tambahkan ke layar utama" di Android, plus nama situs yang konsisten dengan og:site_name.
 *
 * Sengaja tanpa `display`: situs ini bukan PWA, jadi manifest hanya memasok metadata ikon
 * dan nama tanpa mengubah perilaku install di perangkat.
 */
final class WebManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $profile = Setting::profile();
        $name = ($profile['company_name'] ?? '') ?: (string) config('app.name', 'IdeyaWeb');

        return response()
            ->json([
                'name' => $name,
                'short_name' => $name,
                'start_url' => route('home'),
                'background_color' => '#ffffff',
                'theme_color' => '#0a1589',
                'icons' => [
                    ['src' => asset('icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
                    ['src' => asset('icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES)
            ->header('Content-Type', 'application/manifest+json');
    }
}

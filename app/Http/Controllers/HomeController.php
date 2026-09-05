<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\MetaService;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(MetaService $meta): View
    {
        $profile = Setting::profile();

        // SEO: bisa di-override per halaman, contoh ubah title/desc/keywords/canonical/robots
        $seoMeta = $meta->forHome()->generate();

        return view('pages.home', compact('profile', 'seoMeta'));
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $profile = Setting::profile();

        return view('pages.home', compact('profile'));
    }
}

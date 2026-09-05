<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\View\View;

final class HomeController extends Controller
{
    public function __invoke(): View
    {
        $profile = Setting::profile();
        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->orderByDesc('posts_count')->get();
        $tags = Tag::withCount('posts')->orderByDesc('posts_count')->take(16)->get();
        $latestPosts = Post::with(['category', 'tags', 'author'])
            ->published()
            ->latest('published_at')
            ->take(6)
            ->get();
        $featuredPost = $latestPosts->first();
        $popularPosts = Post::with(['category'])
            ->published()
            ->orderByDesc('view_count')
            ->take(4)
            ->get();

        return view('pages.home', compact('profile', 'categories', 'tags', 'latestPosts', 'featuredPost', 'popularPosts'));
    }
}

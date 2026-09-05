<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BlogController extends Controller
{
    public function index(Request $request): View
    {
        $posts = Post::with(['category', 'tags', 'author'])
            ->published()
            ->search($request->string('q')->toString() ?: null)
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('category', fn ($c) => $c->where('slug', $request->string('category')->toString()));
            })
            ->when($request->filled('tag'), function ($q) use ($request) {
                $q->whereHas('tags', fn ($t) => $t->where('slug', $request->string('tag')->toString()));
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('pages.blog.index', compact('posts', 'categories', 'tags'));
    }

    public function show(Post $post): View
    {
        abort_unless($post->status->value === 'published' || auth()->check(), 404);

        $post->load(['category', 'tags', 'author']);
        $post->increment('view_count');

        $related = Post::with(['category'])
            ->published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $categories = Category::withCount(['posts' => fn ($q) => $q->published()])->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('pages.blog.show', compact('post', 'related', 'categories', 'tags'));
    }

    public function category(Category $category): View
    {
        $posts = $category->posts()
            ->with(['category', 'tags', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('pages.blog.category', compact('category', 'posts'));
    }

    public function tag(Tag $tag): View
    {
        $posts = $tag->posts()
            ->with(['category', 'tags', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        return view('pages.blog.tag', compact('tag', 'posts'));
    }
}

<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\MetaService;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BlogController extends Controller
{
    public function index(Request $request, MetaService $meta): View
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

        $seoMeta = $meta->forBlogIndex()->generate();

        return view('pages.blog.index', compact('posts', 'categories', 'tags', 'seoMeta'));
    }

    public function show(Post $post, MetaService $meta): View
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

        $seoMeta = $meta->forPost($post)->generate();

        return view('pages.blog.show', compact('post', 'related', 'categories', 'tags', 'seoMeta'));
    }

    public function category(Category $category, MetaService $meta): View
    {
        $posts = $category->posts()
            ->with(['category', 'tags', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $seoMeta = $meta->set([
            'title' => $category->name,
            'description' => $category->description ?: 'Artikel kategori '.$category->name,
            'url' => route('blog.category', $category),
            'type' => 'website',
        ])->generate();

        return view('pages.blog.category', compact('category', 'posts', 'seoMeta'));
    }

    public function tag(Tag $tag, MetaService $meta): View
    {
        $posts = $tag->posts()
            ->with(['category', 'tags', 'author'])
            ->published()
            ->latest('published_at')
            ->paginate(9);

        $seoMeta = $meta->set([
            'title' => '#'.$tag->name,
            'description' => 'Artikel dengan tag #'.$tag->name,
            'url' => route('blog.tag', $tag),
            'type' => 'website',
        ])->generate();

        return view('pages.blog.tag', compact('tag', 'posts', 'seoMeta'));
    }
}

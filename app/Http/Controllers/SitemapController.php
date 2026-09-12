<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Response;

final class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            ...$this->staticPages(),
            ...$this->posts(),
            ...$this->categories(),
            ...$this->tags(),
        ];

        return response()->view('sitemap', ['urls' => $urls], 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Halaman statis yang selalu tersedia untuk publik.
     *
     * @return list<array{loc: string, lastmod: string|null, changefreq: string|null, priority: float|null}>
     */
    private function staticPages(): array
    {
        return [
            $this->entry(route('home'), 'daily', 1.0),
            $this->entry(route('layanan'), 'monthly', 0.8),
            $this->entry(route('blog.index'), 'daily', 0.8),
            $this->entry(route('kontak'), 'monthly', 0.6),
            $this->entry(route('privacy'), 'yearly', 0.3),
        ];
    }

    /**
     * Post yang sudah dipublikasikan, terbaru lebih dulu.
     *
     * @return list<array{loc: string, lastmod: string|null, changefreq: string|null, priority: float|null}>
     */
    private function posts(): array
    {
        $urls = [];

        $posts = Post::query()
            ->published()
            ->latest('published_at')
            ->get(['slug', 'updated_at']);

        foreach ($posts as $post) {
            $urls[] = $this->entry(route('blog.show', $post), 'weekly', 0.7, $post->updated_at?->toAtomString());
        }

        return $urls;
    }

    /**
     * Kategori yang punya minimal satu post terpublikasi.
     *
     * @return list<array{loc: string, lastmod: string|null, changefreq: string|null, priority: float|null}>
     */
    private function categories(): array
    {
        $urls = [];

        $categories = Category::query()
            ->whereHas('posts', fn ($query) => $query->where('status', PostStatus::Published))
            ->get(['slug', 'updated_at']);

        foreach ($categories as $category) {
            $urls[] = $this->entry(route('blog.category', $category), 'weekly', 0.5, $category->updated_at?->toAtomString());
        }

        return $urls;
    }

    /**
     * Tag yang punya minimal satu post terpublikasi.
     *
     * @return list<array{loc: string, lastmod: string|null, changefreq: string|null, priority: float|null}>
     */
    private function tags(): array
    {
        $urls = [];

        $tags = Tag::query()
            ->whereHas('posts', fn ($query) => $query->where('status', PostStatus::Published))
            ->get(['slug', 'updated_at']);

        foreach ($tags as $tag) {
            $urls[] = $this->entry(route('blog.tag', $tag), 'weekly', 0.4, $tag->updated_at?->toAtomString());
        }

        return $urls;
    }

    /**
     * @return array{loc: string, lastmod: string|null, changefreq: string|null, priority: float|null}
     */
    private function entry(string $loc, ?string $changefreq = null, ?float $priority = null, ?string $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}

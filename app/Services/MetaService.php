<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Support\Str;

final class MetaService
{
    /** @var array<string, mixed> */
    private array $data = [];

    private bool $withDefaults = true;

    /**
     * Override defaults: set title, description, image, url, type, etc.
     *
     * @param  array<string, mixed>  $overrides
     */
    public function set(array $overrides, bool $withDefaults = true): self
    {
        $this->data = $overrides;
        $this->withDefaults = $withDefaults;

        return $this;
    }

    public function title(?string $title): self
    {
        $this->data['title'] = $title;

        return $this;
    }

    public function description(?string $description): self
    {
        $this->data['description'] = $description;

        return $this;
    }

    public function image(?string $image): self
    {
        $this->data['image'] = $image;

        return $this;
    }

    public function url(?string $url): self
    {
        $this->data['url'] = $url;

        return $this;
    }

    /**
     * Convenience: SEO untuk halaman home / landing.
     * Default diambil dari admin/settings: seo_title, seo_description, dll (fallback ke profile).
     *
     * @param  array<string, mixed>  $extra
     */
    public function forHome(array $extra = []): self
    {
        $profile = Setting::profile();
        $seo = Setting::seo();
        $company = $profile['company_name'] ?: (string) config('app.name', 'IdeyaWeb');
        $tagline = $profile['tagline'] ?: 'Digital Agency & IT Solution';
        $about = $profile['about'] ?: 'Membangun produk digital yang cepat & bermakna.';

        $defaultTitle = filled($seo['seo_title']) ? $seo['seo_title'] : $company.' — '.$tagline;
        $defaultDescription = filled($seo['seo_description']) ? $seo['seo_description'] : Str::limit(trim($about), 160);

        return $this->set(array_merge([
            'title' => $defaultTitle,
            'description' => $defaultDescription,
            'keywords' => $seo['seo_keywords'] ?: null,
            'image' => Setting::seoOgImageUrl(),
            'type' => 'website',
            'url' => url()->current(),
        ], $extra));
    }

    /**
     * Convenience: SEO untuk daftar blog.
     *
     * @param  array<string, mixed>  $extra
     */
    public function forBlogIndex(array $extra = []): self
    {
        return $this->set(array_merge([
            'title' => 'Blog',
            'description' => 'Artikel, tutorial, dan insight tentang membangun produk digital — dari ide, desain, hingga scale.',
            'type' => 'website',
            'url' => url()->current(),
        ], $extra));
    }

    /**
     * Convenience: SEO untuk single post.
     */
    public function forPost(Post $post, array $extra = []): self
    {
        $post->loadMissing(['category', 'tags', 'author']);

        return $this->set(array_merge([
            'title' => $post->title,
            'description' => $post->excerpt ?: Str::limit(strip_tags((string) $post->body), 160),
            'image' => $post->imageUrl(),
            'image_alt' => $post->image_caption ?: $post->title,
            'type' => 'article',
            'url' => route('blog.show', $post),
            'published_time' => $post->published_at?->toIso8601String(),
            'author' => $post->author->name ?? null,
            'section' => $post->category->name ?? null,
            'tags' => $post->tags->pluck('name')->all(),
        ], $extra));
    }

    /**
     * Generate array siap pakai untuk <head> (title, meta, og, twitter, canonical, json-ld).
     *
     * @return array<string, mixed>
     */
    public function generate(): array
    {
        $profile = $this->withDefaults ? Setting::profile() : [];
        $seo = $this->withDefaults ? Setting::seo() : [];
        $appName = (string) config('app.name', 'IdeyaWeb');
        $company = $this->withDefaults ? ($profile['company_name'] ?? '') ?: $appName : $appName;

        $seoTitle = $this->withDefaults ? $this->strOrNull($seo['seo_title'] ?? null) : null;
        $seoDescription = $this->withDefaults ? $this->strOrNull($seo['seo_description'] ?? null) : null;
        $seoKeywords = $this->withDefaults ? $this->strOrNull($seo['seo_keywords'] ?? null) : null;
        $seoImage = $this->withDefaults ? Setting::seoOgImageUrl() : null;

        $title = $this->strOrNull($this->data['title'] ?? null) ?? $seoTitle ?? $company;
        $rawDescription = $this->strOrNull($this->data['description'] ?? null)
            ?? $seoDescription
            ?? ($this->withDefaults ? ($profile['about'] ?? null) : null)
            ?? ($company.' — '.(config('app.name') ? $appName : 'Digital Agency & IT Solution'));
        $description = Str::limit(trim((string) $rawDescription), 160);

        $image = $this->strOrNull($this->data['image'] ?? null) ?? $this->strOrNull($seoImage);
        $imageAlt = $this->strOrNull($this->data['image_alt'] ?? null) ?? $title;
        $url = $this->strOrNull($this->data['url'] ?? null) ?? url()->current();
        $type = $this->strOrNull($this->data['type'] ?? null) ?? 'website';
        $locale = str_replace('_', '-', (string) app()->getLocale()) ?: 'id';
        $siteName = $company;

        $robots = $this->strOrNull($this->data['robots'] ?? null) ?? 'index, follow';
        $canonical = $this->strOrNull($this->data['canonical'] ?? null) ?? $url;

        $keywords = $this->data['keywords'] ?? $seoKeywords;
        if (is_array($keywords)) {
            $keywords = implode(', ', array_filter(array_map('strval', $keywords)));
        }
        $keywords = $this->strOrNull($keywords);

        $twitterCard = $image ? 'summary_large_image' : 'summary';
        if (isset($this->data['twitter_card'])) {
            $twitterCard = (string) $this->data['twitter_card'];
        }

        $publishedTime = $this->strOrNull($this->data['published_time'] ?? null);
        $author = $this->strOrNull($this->data['author'] ?? null);
        $section = $this->strOrNull($this->data['section'] ?? null);
        $tags = $this->data['tags'] ?? null;
        if (! is_array($tags)) {
            $tags = null;
        }

        // JSON-LD (Website / Article) — minimal, aman untuk Rich Results
        $jsonLd = null;
        if ($type === 'article') {
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'Article',
                'headline' => $title,
                'description' => $description,
                'author' => $author ? ['@type' => 'Person', 'name' => $author] : null,
                'datePublished' => $publishedTime,
                'image' => $image,
                'mainEntityOfPage' => $url,
            ];
            $jsonLd = array_filter($jsonLd, fn ($v) => $v !== null && $v !== '');
        } else {
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $siteName,
                'url' => $canonical,
                'description' => $description,
            ];
        }

        return [
            'title' => $title,
            'title_full' => $title !== $siteName ? $title.' — '.$siteName : $title,
            'description' => $description,
            'keywords' => $keywords,
            'canonical' => $canonical,
            'robots' => $robots,
            'url' => $url,
            'site_name' => $siteName,
            'locale' => $locale,
            'type' => $type,
            'image' => $image,
            'image_alt' => $imageAlt,
            'twitter_card' => $twitterCard,
            'published_time' => $publishedTime,
            'author' => $author,
            'section' => $section,
            'tags' => $tags,
            'json_ld' => $jsonLd,
        ];
    }

    private function strOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }
}

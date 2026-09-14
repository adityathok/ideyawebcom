<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\DocPage;
use App\Models\DocVersion;
use App\Models\Post;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
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
        $tagline = $profile['tagline'] ?: 'Developer Website & Web App';
        $about = $profile['about'] ?: 'Spesialis membangun web app & app custom, dan berpengalaman mengerjakan WordPress.';

        $defaultTitle = filled($seo['seo_title']) ? $seo['seo_title'] : $company.' — '.$tagline;
        $defaultDescription = filled($seo['seo_description']) ? $seo['seo_description'] : Str::limit(trim($about), 160);

        // `image` sengaja tidak di-set: generate() sudah memakai seo_og_image sebagai
        // default, sehingga og:image:alt ikut memakai nama situs (bukan judul halaman).
        return $this->set(array_merge([
            'title' => $defaultTitle,
            'description' => $defaultDescription,
            'keywords' => $seo['seo_keywords'] ?: null,
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
            'breadcrumbs' => [['name' => 'Blog']],
        ], $extra));
    }

    /**
     * Convenience: SEO untuk single post.
     */
    public function forPost(Post $post, array $extra = []): self
    {
        $post->loadMissing(['category', 'tags', 'author', 'cover']);

        $image = $post->imageUrl();

        return $this->set(array_merge([
            'title' => $post->title,
            'description' => $post->excerpt ?: Str::limit(strip_tags((string) $post->body), 160),
            'image' => $image,
            // Alt hanya dari post kalau post memang punya gambar sendiri; kalau tidak,
            // biarkan null supaya generate() memakai nama situs untuk gambar default.
            'image_alt' => $image !== null ? ($post->image_caption ?: $post->title) : null,
            'type' => 'article',
            'url' => route('blog.show', $post),
            'published_time' => $post->published_at?->toIso8601String(),
            'author' => $post->author->name ?? null,
            'section' => $post->category->name ?? null,
            'tags' => $post->tags->pluck('name')->all(),
            'breadcrumbs' => [
                ['name' => 'Blog', 'url' => route('blog.index')],
                ['name' => $post->title],
            ],
        ], $extra));
    }

    /**
     * Convenience: SEO untuk daftar dokumentasi produk.
     *
     * @param  array<string, mixed>  $extra
     */
    public function forDocsIndex(array $extra = []): self
    {
        return $this->set(array_merge([
            'title' => 'Dokumentasi',
            'description' => 'Panduan, referensi, dan catatan rilis tiap produk — dari pemasangan, konfigurasi, hingga pemecahan masalah.',
            'type' => 'website',
            'url' => url()->current(),
            'breadcrumbs' => [['name' => 'Dokumentasi']],
        ], $extra));
    }

    /**
     * Convenience: SEO untuk dokumentasi satu versi produk.
     *
     * @param  array<string, mixed>  $extra
     */
    public function forDocsVersion(Product $product, DocVersion $version, array $extra = []): self
    {
        return $this->set(array_merge([
            'title' => 'Dokumentasi '.$product->name.' '.$version->label,
            'description' => $product->tagline ?: Str::limit(strip_tags((string) $product->description), 160),
            'type' => 'website',
            'url' => route('docs.version', [$product, $version]),
            'breadcrumbs' => [
                ['name' => 'Dokumentasi', 'url' => route('docs.index')],
                ['name' => $product->name],
                ['name' => $version->label],
            ],
        ], $extra));
    }

    /**
     * Convenience: SEO untuk satu halaman dokumentasi.
     *
     * `type` tetap nilai Open Graph yang sah (`article`) untuk og:type, sedangkan
     * `schema_type` memilih tipe JSON-LD yang lebih spesifik (TechArticle).
     *
     * @param  array<string, mixed>  $extra
     */
    public function forDocPage(Product $product, DocVersion $version, DocPage $page, array $extra = []): self
    {
        return $this->set(array_merge([
            'title' => $page->title,
            'description' => $page->excerpt ?: Str::limit(strip_tags((string) $page->body), 160),
            'type' => 'article',
            'schema_type' => 'tech_article',
            'url' => route('docs.page', [$product, $version, $page]),
            'published_time' => $page->published_at?->toIso8601String(),
            'section' => $product->name.' '.$version->label,
            'breadcrumbs' => [
                ['name' => 'Dokumentasi', 'url' => route('docs.index')],
                ['name' => $product->name],
                ['name' => $version->label],
                ['name' => $page->title],
            ],
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
        $siteName = $company;

        $seoTitle = $this->withDefaults ? $this->strOrNull($seo['seo_title'] ?? null) : null;
        $seoDescription = $this->withDefaults ? $this->strOrNull($seo['seo_description'] ?? null) : null;
        $seoKeywords = $this->withDefaults ? $this->strOrNull($seo['seo_keywords'] ?? null) : null;
        $seoImage = $this->withDefaults ? Setting::seoOgImageUrl() : null;

        $title = $this->strOrNull($this->data['title'] ?? null) ?? $seoTitle ?? $company;
        $siteDescription = $this->siteDescription($seoDescription, $profile, $company);
        $description = $this->resolveDescription($title, $company, $siteDescription);

        // Urutan og:image: gambar khusus halaman → default dari pengaturan (seo_og_image)
        // → banner brand bawaan, supaya tiap halaman selalu punya og:image.
        $pageImage = $this->strOrNull($this->data['image'] ?? null);
        $image = $pageImage
            ?? $this->strOrNull($seoImage)
            ?? ($this->withDefaults ? asset('images/og-logo.jpg') : null);
        // Alt harus mendeskripsikan gambar yang benar-benar dipakai: gambar khusus
        // halaman → judul halaman, gambar default/brand → nama situs.
        $imageAlt = $this->strOrNull($this->data['image_alt'] ?? null)
            ?? ($pageImage !== null ? $title : $siteName);
        [$imageWidth, $imageHeight] = $this->imageDimensions($image);
        $url = $this->strOrNull($this->data['url'] ?? null) ?? url()->current();
        $type = $this->strOrNull($this->data['type'] ?? null) ?? 'website';
        $locale = $this->strOrNull($this->data['locale'] ?? null) ?? $this->defaultOgLocale();

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

        $services = $this->data['services'] ?? [];
        if (! is_array($services)) {
            $services = [];
        }

        $breadcrumbs = $this->data['breadcrumbs'] ?? [];
        if (! is_array($breadcrumbs)) {
            $breadcrumbs = [];
        }

        $isHome = rtrim($canonical, '/') === rtrim(url('/'), '/');

        return [
            'title' => $title,
            'title_full' => $title !== $siteName && ! str_contains($title, $siteName) ? $title.' — '.$siteName : $title,
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
            'image_width' => $imageWidth,
            'image_height' => $imageHeight,
            'twitter_card' => $twitterCard,
            'published_time' => $publishedTime,
            'author' => $author,
            'section' => $section,
            'tags' => $tags,
            'json_ld' => $this->jsonLd([
                'type' => $type,
                'schema_type' => $this->strOrNull($this->data['schema_type'] ?? null),
                'title' => $title,
                'description' => $description,
                'site_description' => $siteDescription,
                'canonical' => $canonical,
                'url' => $url,
                'image' => $image,
                'published_time' => $publishedTime,
                'author' => $author,
                'site_name' => $siteName,
                'language' => str_replace('_', '-', $locale),
                'profile' => $profile,
                'is_home' => $isHome,
                'services' => $services,
                'breadcrumbs' => $breadcrumbs,
            ]),
        ];
    }

    /**
     * Deskripsi meta tidak boleh kosong dan harus unik per halaman.
     *
     * Urutan: `description` eksplisit halaman → default SEO situs (di-awali judul
     * halaman agar tetap unik) → tagline/profil → nama aplikasi.
     */
    private function resolveDescription(string $title, string $company, string $siteDescription): string
    {
        $explicit = $this->strOrNull($this->data['description'] ?? null);
        if ($explicit !== null) {
            return Str::limit($explicit, 160);
        }

        $description = $title !== $company
            ? $title.' — '.$siteDescription
            : $siteDescription;

        return Str::limit($description, 160);
    }

    /**
     * Deskripsi situs (bukan halaman) dari pengaturan SEO → profil → pemanggil.
     *
     * @param  array<string, string>  $profile
     */
    private function siteDescription(?string $seoDescription, array $profile, string $fallback): string
    {
        if (! $this->withDefaults) {
            return $fallback;
        }

        return $seoDescription
            ?? $this->strOrNull($profile['about'] ?? null)
            ?? $this->strOrNull($profile['tagline'] ?? null)
            ?? $fallback;
    }

    /**
     * Susun satu blok `@graph` JSON-LD.
     *
     * Organization & WebSite selalu hadir supaya node halaman bisa merujuk @id
     * mereka tanpa crawler perlu menggabungkan blok terpisah. Node lain menyusul
     * sesuai konteks: WebPage (halaman dalam), Article (post), Service (halaman
     * layanan), dan BreadcrumbList.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function jsonLd(array $context): array
    {
        $graph = [
            $this->organizationSchema($context),
            $this->websiteSchema($context),
        ];

        $schemaType = is_string($context['schema_type'] ?? null) ? $context['schema_type'] : $context['type'];

        if ($schemaType === 'tech_article') {
            $graph[] = $this->techArticleSchema($context);
        } elseif ($context['type'] === 'article') {
            $graph[] = $this->articleSchema($context);
        } elseif (! $context['is_home']) {
            $graph[] = $this->webPageSchema($context);
        }

        foreach ([$this->serviceSchema($context), $this->breadcrumbSchema($context)] as $partial) {
            if ($partial !== null) {
                $graph[] = $partial;
            }
        }

        return [
            '@context' => 'https://schema.org',
            '@graph' => $graph,
        ];
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function organizationSchema(array $context): array
    {
        /** @var array<string, string> $profile */
        $profile = $context['profile'];

        $streetAddress = $this->strOrNull($profile['address'] ?? null);

        $logoUrl = $this->withDefaults ? Setting::publicUrl('logo') : null;
        [$logoWidth, $logoHeight] = $this->imageDimensions($logoUrl);

        return $this->withoutEmpty([
            '@type' => 'Organization',
            '@id' => $this->schemaId('organization'),
            'name' => $context['site_name'],
            'url' => url('/'),
            'description' => $context['site_description'],
            'email' => $this->strOrNull($profile['email'] ?? null),
            'telephone' => $this->strOrNull($profile['phone'] ?? null),
            'address' => $streetAddress === null
                ? null
                : ['@type' => 'PostalAddress', 'streetAddress' => $streetAddress],
            'logo' => $logoUrl === null
                ? null
                : $this->withoutEmpty([
                    '@type' => 'ImageObject',
                    'url' => $logoUrl,
                    'width' => $logoWidth,
                    'height' => $logoHeight,
                ]),
            'sameAs' => array_values(array_filter([
                $this->strOrNull($profile['facebook'] ?? null),
                $this->strOrNull($profile['instagram'] ?? null),
                $this->strOrNull($profile['twitter'] ?? null),
                $this->strOrNull($profile['linkedin'] ?? null),
            ])),
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function websiteSchema(array $context): array
    {
        return $this->withoutEmpty([
            '@type' => 'WebSite',
            '@id' => $this->schemaId('website'),
            'url' => url('/'),
            'name' => $context['site_name'],
            'description' => $context['site_description'],
            'inLanguage' => $context['language'],
            'publisher' => ['@id' => $this->schemaId('organization')],
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function webPageSchema(array $context): array
    {
        return $this->withoutEmpty([
            '@type' => 'WebPage',
            '@id' => $context['canonical'].'#webpage',
            'url' => $context['canonical'],
            'name' => $context['title'],
            'description' => $context['description'],
            'inLanguage' => $context['language'],
            'isPartOf' => ['@id' => $this->schemaId('website')],
        ]);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function articleSchema(array $context): array
    {
        $author = $context['author'];

        return $this->withoutEmpty([
            '@type' => 'Article',
            '@id' => $context['canonical'].'#article',
            'headline' => $context['title'],
            'description' => $context['description'],
            'author' => $author === null ? null : ['@type' => 'Person', 'name' => $author],
            // Google mensyaratkan publisher untuk rich result Article.
            'publisher' => ['@id' => $this->schemaId('organization')],
            'datePublished' => $context['published_time'],
            'image' => $context['image'],
            'mainEntityOfPage' => $context['url'],
            'inLanguage' => $context['language'],
            'isPartOf' => ['@id' => $this->schemaId('website')],
        ]);
    }

    /**
     * TechArticle: `Article` versi teknis (dokumentasi produk). Sama seperti
     * `articleSchema()` tapi tanpa author dan tanpa tag, karena halaman dokumentasi
     * tidak punya penulis per halaman.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>
     */
    private function techArticleSchema(array $context): array
    {
        return $this->withoutEmpty([
            '@type' => 'TechArticle',
            '@id' => $context['canonical'].'#techarticle',
            'headline' => $context['title'],
            'description' => $context['description'],
            // Google mensyaratkan publisher untuk rich result Article.
            'publisher' => ['@id' => $this->schemaId('organization')],
            'datePublished' => $context['published_time'],
            'image' => $context['image'],
            'mainEntityOfPage' => $context['url'],
            'inLanguage' => $context['language'],
            'isPartOf' => ['@id' => $this->schemaId('website')],
        ]);
    }

    /**
     * Service + katalog layanan, hanya bila halaman mengirim meta key `services`.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>|null
     */
    private function serviceSchema(array $context): ?array
    {
        /** @var array<int, mixed> $services */
        $services = $context['services'];

        $offers = [];

        foreach ($services as $service) {
            if (! is_array($service)) {
                continue;
            }

            $name = $this->strOrNull($service['title'] ?? null);

            if ($name === null) {
                continue;
            }

            $offers[] = $this->withoutEmpty([
                '@type' => 'Offer',
                'itemOffered' => $this->withoutEmpty([
                    '@type' => 'Service',
                    'name' => $name,
                    'description' => $this->strOrNull($service['short'] ?? null)
                        ?? $this->strOrNull($service['desc'] ?? null),
                ]),
            ]);
        }

        if ($offers === []) {
            return null;
        }

        $name = 'Layanan '.$context['site_name'];
        $canonical = $context['canonical'];

        return $this->withoutEmpty([
            '@type' => 'Service',
            '@id' => $canonical.'#service',
            'name' => $name,
            'serviceType' => $context['title'],
            'description' => $context['description'],
            'url' => $canonical,
            'provider' => ['@id' => $this->schemaId('organization')],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => $name,
                'itemListElement' => $offers,
            ],
        ]);
    }

    /**
     * BreadcrumbList dari meta key `breadcrumbs` — list `['name' => ..., 'url' => ...]`.
     *
     * "Beranda" ditambahkan otomatis; URL item terakhir default ke canonical halaman
     * supaya struktur tetap valid walau pemanggil tidak mengirim URL-nya.
     *
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>|null
     */
    private function breadcrumbSchema(array $context): ?array
    {
        /** @var array<int, mixed> $breadcrumbs */
        $breadcrumbs = $context['breadcrumbs'];

        if ($breadcrumbs === []) {
            return null;
        }

        $items = [['name' => 'Beranda', 'url' => route('home')]];

        foreach ($breadcrumbs as $breadcrumb) {
            if (! is_array($breadcrumb)) {
                continue;
            }

            $name = $this->strOrNull($breadcrumb['name'] ?? null);

            if ($name === null) {
                continue;
            }

            $items[] = ['name' => $name, 'url' => $this->strOrNull($breadcrumb['url'] ?? null)];
        }

        $lastIndex = array_key_last($items);
        $elements = [];

        foreach ($items as $index => $item) {
            $elements[] = $this->withoutEmpty([
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? ($index === $lastIndex ? $context['canonical'] : null),
            ]);
        }

        return $this->withoutEmpty([
            '@type' => 'BreadcrumbList',
            '@id' => $context['canonical'].'#breadcrumb',
            'itemListElement' => $elements,
        ]);
    }

    private function schemaId(string $fragment): string
    {
        return url('/').'#'.$fragment;
    }

    /**
     * Buang nilai kosong (null, '', []) tanpa mengubah urutan key, supaya JSON-LD
     * hanya memuat properti yang benar-benar ada isinya.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    private function withoutEmpty(array $schema): array
    {
        return array_filter(
            $schema,
            static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== [],
        );
    }

    /**
     * Dimensi og:image — hanya kalau gambarnya file lokal yang bisa dibaca.
     *
     * URL remote (CDN) sengaja dilewati supaya render halaman tidak menunggu
     * request luar; crawler tetap membaca dimensinya sendiri saat itu.
     *
     * @return array{0: int|null, 1: int|null}
     */
    private function imageDimensions(?string $image): array
    {
        $file = $this->localImagePath($image);

        if ($file === null) {
            return [null, null];
        }

        $size = @getimagesize($file);

        if ($size === false) {
            return [null, null];
        }

        $width = (int) ($size[0] ?? 0);
        $height = (int) ($size[1] ?? 0);

        return [$width > 0 ? $width : null, $height > 0 ? $height : null];
    }

    /**
     * og:locale wajib berformat language_TERRITORY (`id_ID`), bukan `id-ID`.
     *
     * Locale aplikasi yang sudah membawa region dihormati apa adanya. Kode bahasa
     * tanpa region (`en` bawaan Laravel, atau `id`) tidak cukup untuk og:locale:
     * seluruh konten publik situs ini berbahasa Indonesia, jadi default-nya `id_ID`.
     */
    private function defaultOgLocale(): string
    {
        $locale = str_replace('-', '_', (string) app()->getLocale());

        return str_contains($locale, '_') ? $locale : 'id_ID';
    }

    /**
     * Petakan URL gambar kembali ke file lokal, kalau memang file kita sendiri.
     *
     * Prefix storage diperiksa lebih dulu karena root publik adalah prefix yang
     * lebih pendek dan ikut cocok untuk URL storage.
     */
    private function localImagePath(?string $image): ?string
    {
        if ($image === null) {
            return null;
        }

        /** @var FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        $bases = [
            [$disk->url(''), $disk->path('')],
            [asset(''), public_path()],
        ];

        foreach ($bases as [$urlBase, $root]) {
            $urlBase = rtrim($urlBase, '/');
            $root = rtrim($root, '/\\');

            if ($urlBase === '' || ! str_starts_with($image, $urlBase.'/')) {
                continue;
            }

            $file = $root.'/'.ltrim(substr($image, strlen($urlBase) + 1), '/');

            if (is_file($file)) {
                return $file;
            }
        }

        return null;
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

<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\MetaService;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Menjalankan $callback dengan aset public/images/og-{slug}.{ext} dalam kondisi tertentu.
 * Aset asli (kalau ada) dipindah sementara ke *.phpunit-bak lalu dikembalikan,
 * supaya tes tidak rapuh terhadap file yang nanti ditambahkan manual.
 */
function withPageOgImage(string $slug, ?string $extension, Closure $callback, ?string $contents = null): void
{
    $directory = public_path('images');
    $extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $backups = [];

    foreach ($extensions as $candidate) {
        $path = "{$directory}/og-{$slug}.{$candidate}";

        if (File::isFile($path)) {
            $backup = "{$path}.phpunit-bak";
            File::move($path, $backup);
            $backups[$candidate] = $backup;
        }
    }

    if ($extension !== null) {
        // Fixture 2000x1333 — sengaja beda dari kartu brand (1200x630) supaya tes
        // dimensi membuktikan angkanya diambil dari gambar halaman itu sendiri.
        File::put("{$directory}/og-{$slug}.{$extension}", $contents ?? File::get(public_path('images/page-hero-sky.jpg')));
    }

    try {
        $callback();
    } finally {
        foreach ($extensions as $candidate) {
            $created = "{$directory}/og-{$slug}.{$candidate}";

            if (File::isFile($created)) {
                File::delete($created);
            }
        }

        foreach ($backups as $candidate => $backup) {
            File::move($backup, "{$directory}/og-{$slug}.{$candidate}");
        }
    }
}

function ogDescriptionFromHtml(string $html): ?string
{
    preg_match('/<meta property="og:description" content="([^"]*)"/', $html, $matches);

    return $matches[1] ?? null;
}

function ogImageFromHtml(string $html): ?string
{
    preg_match('/<meta property="og:image" content="([^"]*)"/', $html, $matches);

    return $matches[1] ?? null;
}

/** URL absolut untuk gambar post di disk public — sama seperti Post::imageUrl(). */
function postImageUrl(string $path): string
{
    /** @var FilesystemAdapter $disk */
    $disk = Storage::disk('public');

    return $disk->url($path);
}

/**
 * Menjalankan $callback dengan gambar sementara di disk public, lalu mengembalikan
 * isi sebelumnya (atau menghapusnya kalau file itu memang belum ada).
 */
function withStoredImage(string $path, Closure $callback, ?string $contents = null): void
{
    $disk = Storage::disk('public');
    $existed = $disk->exists($path);
    $original = $existed ? $disk->get($path) : null;

    $disk->put($path, $contents ?? File::get(public_path('images/page-hero-sky.jpg')));

    try {
        $callback();
    } finally {
        if ($existed) {
            $disk->put($path, (string) $original);
        } else {
            $disk->delete($path);
        }
    }
}

test('renders a non-empty og:description on every public page', function () {
    $category = Category::factory()->create(['name' => 'Laravel', 'description' => 'Artikel seputar Laravel.']);
    $tag = Tag::factory()->create(['name' => 'Testing']);
    $post = Post::factory()->published()->create([
        'title' => 'Panduan Web App',
        'excerpt' => 'Ringkasan panduan membangun web app.',
    ]);

    $pages = [
        'home' => route('home'),
        'layanan' => route('layanan'),
        'kontak' => route('kontak'),
        'privacy' => route('privacy'),
        'blog index' => route('blog.index'),
        'blog show' => route('blog.show', $post),
        'blog category' => route('blog.category', $category),
        'blog tag' => route('blog.tag', $tag),
    ];

    $descriptions = [];

    foreach ($pages as $label => $url) {
        $description = ogDescriptionFromHtml($this->get($url)->assertOk()->getContent());

        expect($description)->not->toBeEmpty("og:description kosong atau hilang di halaman {$label}");

        $descriptions[$label] = $description;
    }

    expect($descriptions)->toHaveCount(count($pages));
    expect(array_unique($descriptions))->toHaveCount(count($pages), 'og:description antar halaman harus unik');
});

test('falls back to a per-page description when no description is set', function () {
    Setting::set('seo_description', 'Solusi website & web app untuk bisnis Anda.');

    $blog = app(MetaService::class)->set(['title' => 'Blog'])->generate();
    $layanan = app(MetaService::class)->set(['title' => 'Layanan'])->generate();

    expect($blog['description'])->toContain('Blog')
        ->and($blog['description'])->toContain('Solusi website & web app untuk bisnis Anda.')
        ->and($layanan['description'])->toContain('Layanan')
        ->and($blog['description'])->not->toBe($layanan['description']);
});

test('uses each page description instead of the global profile text', function () {
    Setting::set('company_name', 'IdeyaWeb');
    Setting::set('tagline', 'Digital Agency & IT Solution');
    Setting::set('about', 'IdeyaWeb adalah digital agency yang fokus pada website, aplikasi, dan solusi IT.');

    $category = Category::factory()->create(['name' => 'Laravel', 'description' => 'Artikel seputar Laravel.']);
    $tag = Tag::factory()->create(['name' => 'Testing']);

    $home = ogDescriptionFromHtml($this->get(route('home'))->getContent());
    $blog = ogDescriptionFromHtml($this->get(route('blog.index'))->getContent());
    $categoryDescription = ogDescriptionFromHtml($this->get(route('blog.category', $category))->getContent());
    $tagDescription = ogDescriptionFromHtml($this->get(route('blog.tag', $tag))->getContent());

    expect($home)->toContain('IdeyaWeb adalah digital agency')
        ->and($blog)->toContain('Artikel, tutorial')
        ->and($categoryDescription)->toContain('Artikel seputar Laravel.')
        ->and($tagDescription)->toContain('#Testing');
});

test('always produces a non-empty description even without settings', function () {
    $meta = app(MetaService::class)->set(['title' => 'Blog'])->generate();

    expect($meta['description'])->not->toBeEmpty();
});

test('renders a non-empty absolute og:image on every public page', function () {
    $category = Category::factory()->create(['name' => 'Laravel']);
    $tag = Tag::factory()->create(['name' => 'Testing']);
    $post = Post::factory()->published()->create(['title' => 'Panduan Web App', 'image' => 'posts/cover.jpg']);

    $pages = [
        'home' => route('home'),
        'layanan' => route('layanan'),
        'kontak' => route('kontak'),
        'privacy' => route('privacy'),
        'blog index' => route('blog.index'),
        'blog show' => route('blog.show', $post),
        'blog category' => route('blog.category', $category),
        'blog tag' => route('blog.tag', $tag),
    ];

    foreach ($pages as $label => $url) {
        $image = ogImageFromHtml($this->get($url)->assertOk()->getContent());

        expect($image)->not->toBeEmpty("og:image kosong atau hilang di halaman {$label}");
        expect($image)->toMatch('/^https?:\/\//', "og:image harus URL absolut di halaman {$label}");
    }
});

test('falls back to the default og image for a post without its own image', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    $post = Post::factory()->published()->create([
        'title' => 'Post Tanpa Gambar',
        'image' => null,
        'cover_image' => null,
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('property="og:image" content="https://cdn.example.com/og-default.jpg"', false);
});

test('keeps the post image over the default when the post has one', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    $post = Post::factory()->published()->create([
        'title' => 'Post Bergambar',
        'image' => 'posts/cover.jpg',
    ]);

    $expected = 'property="og:image" content="'.postImageUrl('posts/cover.jpg').'"';

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee($expected, false)
        ->assertDontSee('property="og:image" content="https://cdn.example.com/og-default.jpg"', false);
});

test('uses the site name as og:image alt when the default image is used', function () {
    Setting::set('company_name', 'IdeyaWeb');
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    $post = Post::factory()->published()->create([
        'title' => 'Post Tanpa Gambar',
        'image' => null,
        'cover_image' => null,
    ]);

    $expected = 'property="og:image:alt" content="IdeyaWeb"';

    $pages = [route('home'), route('blog.index'), route('layanan'), route('blog.show', $post)];

    foreach ($pages as $url) {
        $this->get($url)->assertOk()->assertSee($expected, false);
    }
});

test('uses the post title as og:image alt when the post has its own image', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Panduan Web App',
        'image' => 'posts/cover.jpg',
        'image_caption' => null,
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('property="og:image:alt" content="Panduan Web App"', false);
});

test('uses the image caption as og:image alt when the post provides one', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Panduan Web App',
        'image' => 'posts/cover.jpg',
        'image_caption' => 'Tangkapan layar dashboard',
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('property="og:image:alt" content="Tangkapan layar dashboard"', false);
});

test('renders og:image dimensions for the default brand image', function () {
    foreach ([route('home'), route('blog.index')] as $url) {
        $this->get($url)
            ->assertOk()
            ->assertSee('<meta property="og:image:width" content="1200" />', false)
            ->assertSee('<meta property="og:image:height" content="630" />', false);
    }
});

test('renders og:image dimensions for a page specific image', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    withPageOgImage('layanan', 'jpg', function () {
        $this->get(route('layanan'))
            ->assertOk()
            ->assertSee('<meta property="og:image:width" content="2000" />', false)
            ->assertSee('<meta property="og:image:height" content="1333" />', false);
    });
});

test('renders og:image dimensions for a post image stored on the public disk', function () {
    $post = Post::factory()->published()->create([
        'title' => 'Panduan Web App',
        'image' => 'posts/cover.jpg',
    ]);

    withStoredImage('posts/cover.jpg', function () use ($post) {
        $this->get(route('blog.show', $post))
            ->assertOk()
            ->assertSee('<meta property="og:image:width" content="2000" />', false)
            ->assertSee('<meta property="og:image:height" content="1333" />', false);
    });
});

test('omits og:image dimensions for a remote og image', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('property="og:image" content="https://cdn.example.com/og-default.jpg"', false)
        ->assertDontSee('og:image:width', false);
});

test('omits og:image dimensions when the image file cannot be read', function () {
    withPageOgImage('layanan', 'png', function () {
        $this->get(route('layanan'))
            ->assertOk()
            ->assertSee('og-layanan.png', false)
            ->assertDontSee('og:image:width', false);
    }, contents: 'bukan gambar');
});

test('uses the og image from settings as the default for pages without their own image', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    $expected = 'property="og:image" content="https://cdn.example.com/og-default.jpg"';

    foreach ([route('home'), route('blog.index')] as $url) {
        $this->get($url)->assertOk()->assertSee($expected, false);
    }
});

test('falls back to the bundled brand image when no og image is configured', function () {
    $expected = 'property="og:image" content="'.asset('images/og-logo.jpg').'"';

    foreach ([route('home'), route('blog.index')] as $url) {
        $this->get($url)->assertOk()->assertSee($expected, false);
    }
});

test('prefers a page specific og image over the configured default', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    // jpg dan webp sekaligus memastikan resolver mengenali beberapa ekstensi.
    $pages = [
        'layanan' => 'jpg',
        'kontak' => 'webp',
    ];

    foreach ($pages as $slug => $extension) {
        withPageOgImage($slug, $extension, function () use ($slug, $extension) {
            $expected = 'property="og:image" content="'.asset("images/og-{$slug}.{$extension}").'"';

            $this->get(route($slug))
                ->assertOk()
                ->assertSee($expected, false)
                ->assertDontSee('property="og:image" content="https://cdn.example.com/og-default.jpg"', false);
        });
    }
});

test('falls back to the configured default when a page has no og image asset', function () {
    Setting::set('seo_og_image', 'https://cdn.example.com/og-default.jpg');

    $expected = 'property="og:image" content="https://cdn.example.com/og-default.jpg"';

    foreach (['layanan', 'kontak', 'privacy'] as $slug) {
        withPageOgImage($slug, null, function () use ($slug, $expected) {
            $this->get(route($slug))->assertOk()->assertSee($expected, false);
        });
    }
});

test('renders og:locale as id_ID', function () {
    foreach ([route('home'), route('blog.index')] as $url) {
        $this->get($url)
            ->assertOk()
            ->assertSee('<meta property="og:locale" content="id_ID" />', false);
    }
});

test('keeps language_TERRITORY format when the app locale carries a region', function () {
    app()->setLocale('en-US');

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<meta property="og:locale" content="en_US" />', false)
        ->assertDontSee('<meta property="og:locale" content="en-US" />', false);
});

test('falls back to id_ID when the app locale has no region', function (string $locale) {
    app()->setLocale($locale);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('<meta property="og:locale" content="id_ID" />', false);
})->with([
    'laravel default' => 'en',
    'bare indonesian' => 'id',
]);

test('lets a page override og:locale', function () {
    $meta = app(MetaService::class)->set(['locale' => 'en_US'])->generate();

    expect($meta['locale'])->toBe('en_US');
});

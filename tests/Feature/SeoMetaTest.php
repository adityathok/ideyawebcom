<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\MetaService;

function ogDescriptionFromHtml(string $html): ?string
{
    preg_match('/<meta property="og:description" content="([^"]*)"/', $html, $matches);

    return $matches[1] ?? null;
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

<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;

/** Blok JSON-LD pertama di <head>, sudah di-decode jadi array. */
function jsonLdFromHtml(string $html): array
{
    preg_match('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $matches);

    return json_decode($matches[1] ?? '{}', true) ?: [];
}

/** Node pertama di dalam `@graph` dengan `@type` tertentu. */
function schemaNode(array $jsonLd, string $type): ?array
{
    foreach ($jsonLd['@graph'] ?? [] as $node) {
        if (($node['@type'] ?? null) === $type) {
            return $node;
        }
    }

    return null;
}

test('renders only the Organization and WebSite nodes on the home page', function () {
    $jsonLd = jsonLdFromHtml($this->get(route('home'))->getContent());

    expect($jsonLd['@context'])->toBe('https://schema.org')
        ->and(array_column($jsonLd['@graph'], '@type'))->toBe(['Organization', 'WebSite']);

    $organization = schemaNode($jsonLd, 'Organization');
    $website = schemaNode($jsonLd, 'WebSite');

    expect($organization['@id'])->toBe(url('/').'#organization')
        ->and($website['@id'])->toBe(url('/').'#website')
        ->and($website['url'])->toBe(url('/'))
        ->and($website['publisher'])->toBe(['@id' => url('/').'#organization']);
});

test('builds the Organization node from the site profile and omits empty fields', function () {
    Setting::set('company_name', 'IdeyaWeb');
    Setting::set('about', 'IdeyaWeb adalah digital agency yang fokus pada website dan aplikasi.');
    Setting::set('email', 'halo@ideyaweb.test');
    Setting::set('phone', '+62 812 0000 0000');
    Setting::set('address', 'Jl. Merdeka No. 1, Jakarta');
    Setting::set('facebook', 'https://facebook.com/ideyaweb');
    Setting::set('instagram', 'https://instagram.com/ideyaweb');

    $organization = schemaNode(jsonLdFromHtml($this->get(route('home'))->getContent()), 'Organization');

    expect($organization['name'])->toBe('IdeyaWeb')
        ->and($organization['url'])->toBe(url('/'))
        ->and($organization['description'])->toBe('IdeyaWeb adalah digital agency yang fokus pada website dan aplikasi.')
        ->and($organization['email'])->toBe('halo@ideyaweb.test')
        ->and($organization['telephone'])->toBe('+62 812 0000 0000')
        ->and($organization['address'])->toBe([
            '@type' => 'PostalAddress',
            'streetAddress' => 'Jl. Merdeka No. 1, Jakarta',
        ])
        ->and($organization['sameAs'])->toBe([
            'https://facebook.com/ideyaweb',
            'https://instagram.com/ideyaweb',
        ])
        ->and($organization)->not->toHaveKey('logo');
});

test('includes the configured logo in the Organization node', function () {
    Setting::set('logo', 'https://cdn.example.com/logo.png');

    $organization = schemaNode(jsonLdFromHtml($this->get(route('home'))->getContent()), 'Organization');

    expect($organization['logo'])->toBe([
        '@type' => 'ImageObject',
        'url' => 'https://cdn.example.com/logo.png',
    ]);
});

test('lists every service of the layanan page in the Service catalog', function () {
    $service = schemaNode(jsonLdFromHtml($this->get(route('layanan'))->getContent()), 'Service');

    expect($service['@id'])->toBe(route('layanan').'#service')
        ->and($service['url'])->toBe(route('layanan'))
        ->and($service['serviceType'])->toBe('Layanan')
        ->and($service['provider'])->toBe(['@id' => url('/').'#organization'])
        ->and($service['hasOfferCatalog']['@type'])->toBe('OfferCatalog');

    $items = $service['hasOfferCatalog']['itemListElement'];

    expect($items)->toHaveCount(6)
        ->and($items[0]['@type'])->toBe('Offer')
        ->and($items[0]['itemOffered'])->toBe([
            '@type' => 'Service',
            'name' => 'Web App & App Custom',
            'description' => 'Dashboard, sistem & aplikasi bisnis',
        ])
        ->and(array_column(array_column($items, 'itemOffered'), 'name'))->toBe([
            'Web App & App Custom',
            'Website Company Profile',
            'WordPress Development',
            'Optimasi & Percepatan WordPress',
            'API & Integrasi Web',
            'Maintenance Website',
        ]);
});

test('omits the Service node on pages without a service catalog', function () {
    $pages = [
        'home' => route('home'),
        'kontak' => route('kontak'),
        'privacy' => route('privacy'),
        'blog index' => route('blog.index'),
    ];

    foreach ($pages as $label => $url) {
        $service = schemaNode(jsonLdFromHtml($this->get($url)->getContent()), 'Service');

        expect($service)->toBeNull("Halaman {$label} tidak boleh punya node Service");
    }
});

test('renders a two level breadcrumb on the layanan page', function () {
    $breadcrumb = schemaNode(jsonLdFromHtml($this->get(route('layanan'))->getContent()), 'BreadcrumbList');

    expect($breadcrumb['@id'])->toBe(route('layanan').'#breadcrumb')
        ->and($breadcrumb['itemListElement'])->toBe([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Layanan', 'item' => route('layanan')],
        ]);
});

test('renders a three level breadcrumb on a blog post', function () {
    $post = Post::factory()->published()->create(['title' => 'Panduan Web App']);

    $breadcrumb = schemaNode(jsonLdFromHtml($this->get(route('blog.show', $post))->getContent()), 'BreadcrumbList');

    expect($breadcrumb['itemListElement'])->toBe([
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => route('home')],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => route('blog.index')],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Panduan Web App', 'item' => route('blog.show', $post)],
    ]);
});

test('points the Article node at the Organization publisher and the WebSite', function () {
    $post = Post::factory()->published()->create(['title' => 'Panduan Web App']);

    $article = schemaNode(jsonLdFromHtml($this->get(route('blog.show', $post))->getContent()), 'Article');

    expect($article['@id'])->toBe(route('blog.show', $post).'#article')
        ->and($article['headline'])->toBe('Panduan Web App')
        ->and($article['publisher'])->toBe(['@id' => url('/').'#organization'])
        ->and($article['isPartOf'])->toBe(['@id' => url('/').'#website']);
});

test('marks an inner page as a WebPage within the WebSite', function (string $routeName) {
    $url = route($routeName);

    $webPage = schemaNode(jsonLdFromHtml($this->get($url)->getContent()), 'WebPage');

    expect($webPage['@id'])->toBe($url.'#webpage')
        ->and($webPage['url'])->toBe($url)
        ->and($webPage['name'])->not->toBeEmpty()
        ->and($webPage['description'])->not->toBeEmpty()
        ->and($webPage['isPartOf'])->toBe(['@id' => url('/').'#website'])
        ->and($webPage['inLanguage'])->toBe('id-ID');
})->with(['layanan', 'kontak', 'privacy', 'blog.index']);

test('renders an Organization and WebSite node on every public page', function () {
    $category = Category::factory()->create(['name' => 'Laravel']);
    $tag = Tag::factory()->create(['name' => 'Testing']);
    $post = Post::factory()->published()->create(['title' => 'Panduan Web App']);

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
        $jsonLd = jsonLdFromHtml($this->get($url)->getContent());

        expect($jsonLd['@context'] ?? null)->toBe('https://schema.org', "JSON-LD hilang di halaman {$label}");
        expect(schemaNode($jsonLd, 'Organization'))->not->toBeNull("Organization hilang di halaman {$label}");
        expect(schemaNode($jsonLd, 'WebSite'))->not->toBeNull("WebSite hilang di halaman {$label}");
    }
});

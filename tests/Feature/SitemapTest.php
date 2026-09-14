<?php

use App\Models\Category;
use App\Models\DocPage;
use App\Models\Post;
use App\Models\Product;
use App\Models\Tag;

test('renders the sitemap with every public page', function () {
    $this->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(route('home'))
        ->assertSee(route('layanan'))
        ->assertSee(route('blog.index'))
        ->assertSee(route('kontak'))
        ->assertSee(route('privacy'));
});

test('lists published posts and hides drafts', function () {
    $published = Post::factory()->published()->create();
    $draft = Post::factory()->draft()->create();

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('blog.show', $published))
        ->assertDontSee(route('blog.show', $draft));
});

test('serves well-formed xml', function () {
    Post::factory()->published()->create();

    $content = $this->get(route('sitemap'))->assertOk()->getContent();

    $xml = simplexml_load_string($content);

    expect($xml)->not->toBeFalse()
        ->and($xml->getName())->toBe('urlset');
});

test('lists published documentation versions and pages, hiding drafts', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $published = DocPage::factory()->published()->create(['version_id' => $version->id]);
    $draft = DocPage::factory()->draft()->create(['version_id' => $version->id]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('docs.index'))
        ->assertSee(route('docs.version', [$product, $version]))
        ->assertSee(route('docs.page', [$product, $version, $published]))
        ->assertDontSee(route('docs.page', [$product, $version, $draft]));
});

test('hides documentation of products that are not published', function () {
    $product = Product::factory()->create(['is_published' => false]);
    $version = $product->versions()->firstOrFail();
    DocPage::factory()->published()->create(['version_id' => $version->id]);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertDontSee(route('docs.version', [$product, $version]));
});

test('lists only categories and tags that have a published post', function () {
    $category = Category::factory()->create();
    $tag = Tag::factory()->create(['name' => 'Laravel']);
    $emptyCategory = Category::factory()->create();
    $emptyTag = Tag::factory()->create(['name' => 'Kosong']);

    Post::factory()->published()->create(['category_id' => $category->id])->tags()->attach($tag);

    $this->get(route('sitemap'))
        ->assertOk()
        ->assertSee(route('blog.category', $category))
        ->assertSee(route('blog.tag', $tag))
        ->assertDontSee(route('blog.category', $emptyCategory))
        ->assertDontSee(route('blog.tag', $emptyTag));
});

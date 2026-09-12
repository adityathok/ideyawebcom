<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;

test('renders the blog index with published posts', function () {
    $post = Post::factory()->published()->create(['title' => 'Panduan Web App']);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('Blog')
        ->assertSee('Panduan Web App')
        ->assertSee(route('blog.show', $post), false);
});

test('links to the blog from the public header navigation', function () {
    Post::factory()->published()->create();

    // The menu item is exposed across public pages, inactive off the blog.
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(route('blog.index'), false)
        ->assertSee('hover:text-[#100f12]">Blog</a>', false);

    // Blog pages highlight it as the active item.
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('bg-[#f3f6ff] text-[#0a1589]">Blog</a>', false);
});

test('renders the blog index cards without the post author or view count', function () {
    $author = User::factory()->create(['name' => 'Budi Penulis']);
    Post::factory()->published()->create(['user_id' => $author->id, 'view_count' => 1234]);

    $this->get(route('blog.index'))
        ->assertOk()
        ->assertDontSee('Budi Penulis')
        ->assertDontSee('1234 views');
});

test('applies the rootly design system to the blog index', function () {
    Post::factory()->published()->create();

    $response = $this->get(route('blog.index'))->assertOk();

    // Deep-blue primary CTA, blue tint surfaces, and hairline borders (DESIGN.md).
    $response->assertSee('0a1589', false);
    $response->assertSee('f3f6ff', false);
    $response->assertSee('e3eaff', false);
    $response->assertSee('100f12', false);

    // The old cream/off-white palette is gone.
    $response->assertDontSee('f5f1ec', false);
    $response->assertDontSee('ebe7e1', false);
});

test('applies the rootly design system to pagination', function () {
    Post::factory()->published()->count(15)->create();

    $response = $this->get(route('blog.index'))->assertOk();

    // Active page uses the deep-blue primary (DESIGN.md).
    $response->assertSee('0a1589', false);

    // The old cream palette is gone.
    $response->assertDontSee('f5f1ec', false);
    $response->assertDontSee('d3cec6', false);
    $response->assertDontSee('7b7b78', false);
});

test('renders the blog index empty state', function () {
    $this->get(route('blog.index'))
        ->assertOk()
        ->assertSee('Tidak ada artikel ditemukan');
});

test('renders the blog index with active filters', function () {
    $category = Category::factory()->create(['name' => 'Laravel']);
    $tag = Tag::factory()->create(['name' => 'Testing']);
    $post = Post::factory()->published()->create(['category_id' => $category->id]);
    $post->tags()->attach($tag);

    $this->get(route('blog.index', ['category' => $category->slug, 'tag' => $tag->slug]))
        ->assertOk()
        ->assertSee('kategori: '.$category->slug)
        ->assertSee('#'.$tag->slug);
});

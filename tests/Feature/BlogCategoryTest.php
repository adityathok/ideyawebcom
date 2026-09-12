<?php

use App\Models\Category;
use App\Models\Post;

test('renders a category archive with its published posts', function () {
    $category = Category::factory()->create(['name' => 'Laravel']);
    $post = Post::factory()->published()->create(['category_id' => $category->id]);

    $this->get(route('blog.category', $category))
        ->assertOk()
        ->assertSee('Laravel')
        ->assertSee($post->title);
});

test('renders an empty category archive', function () {
    $category = Category::factory()->create();

    $this->get(route('blog.category', $category))
        ->assertOk()
        ->assertSee('Belum ada artikel di kategori ini');
});

test('applies the rootly design system to the category archive', function () {
    $category = Category::factory()->create();

    $response = $this->get(route('blog.category', $category))->assertOk();

    // Deep-blue primary, blue tint surfaces, and hairline borders (DESIGN.md).
    $response->assertSee('0a1589', false);
    $response->assertSee('f3f6ff', false);
    $response->assertSee('e3eaff', false);

    // The old cream palette is gone.
    $response->assertDontSee('f5f1ec', false);
    $response->assertDontSee('ebe7e1', false);
});

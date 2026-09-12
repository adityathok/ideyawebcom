<?php

use App\Models\Post;
use App\Models\Tag;

test('renders a tag archive with its published posts', function () {
    $tag = Tag::factory()->create(['name' => 'Testing']);
    $post = Post::factory()->published()->create();
    $post->tags()->attach($tag);

    $this->get(route('blog.tag', $tag))
        ->assertOk()
        ->assertSee('#Testing')
        ->assertSee($post->title);
});

test('renders an empty tag archive', function () {
    $tag = Tag::factory()->create();

    $this->get(route('blog.tag', $tag))
        ->assertOk()
        ->assertSee('Belum ada artikel dengan tag ini');
});

test('applies the rootly design system to the tag archive', function () {
    $tag = Tag::factory()->create();

    $response = $this->get(route('blog.tag', $tag))->assertOk();

    // Deep-blue primary, blue tint surfaces, and hairline borders (DESIGN.md).
    $response->assertSee('0a1589', false);
    $response->assertSee('f3f6ff', false);
    $response->assertSee('e3eaff', false);

    // The old cream palette is gone.
    $response->assertDontSee('f5f1ec', false);
    $response->assertDontSee('ebe7e1', false);
});

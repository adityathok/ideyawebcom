<?php

use App\Models\Post;

test('renders an html post body without escaping it', function () {
    $post = Post::factory()->published()->create([
        'body' => '<p>Halo <strong>dunia</strong></p>',
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('<strong>dunia</strong>', false);
});

test('keeps line breaks when the post body is plain text', function () {
    $post = Post::factory()->published()->create([
        'body' => "Baris satu\nBaris dua",
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('Baris satu<br', false)
        ->assertSee('Baris dua');
});

test('applies the rootly design system to a post page', function () {
    $post = Post::factory()->published()->create();

    $response = $this->get(route('blog.show', $post))->assertOk();

    // Deep-blue primary and hairline borders (DESIGN.md).
    $response->assertSee('0a1589', false);
    $response->assertSee('e3eaff', false);

    // The old cream palette is gone.
    $response->assertDontSee('ebe7e1', false);
    $response->assertDontSee('d3cec6', false);
});

<?php

use App\Models\Post;
use Illuminate\Support\Facades\Storage;

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

test('uses the post image as the og:image', function () {
    $post = Post::factory()->published()->create([
        'image' => 'posts/og-cover.jpg',
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('property="og:image" content="'.Storage::disk('public')->url('posts/og-cover.jpg').'"', false);
});

test('uses an absolute cover image as the og:image', function () {
    $post = Post::factory()->published()->create([
        'image' => null,
        'cover_image' => 'https://cdn.example.com/cover.jpg',
    ]);

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('property="og:image" content="https://cdn.example.com/cover.jpg"', false);
});

test('renders social share buttons for the post', function () {
    $post = Post::factory()->published()->create(['title' => 'Panduan Web App']);

    $shareUrl = urlencode(route('blog.show', $post));
    $shareTitle = urlencode('Panduan Web App');

    $this->get(route('blog.show', $post))
        ->assertOk()
        ->assertSee('https://www.facebook.com/sharer/sharer.php?u='.$shareUrl, false)
        ->assertSee('https://www.linkedin.com/sharing/share-offsite/?url='.$shareUrl, false)
        ->assertSee('https://wa.me/?text='.$shareTitle.'%20'.$shareUrl, false)
        ->assertSee('https://twitter.com/intent/tweet?url='.$shareUrl.'&amp;text='.$shareTitle, false);
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

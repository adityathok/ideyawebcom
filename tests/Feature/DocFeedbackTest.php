<?php

use App\Models\DocFeedback;
use App\Models\DocPage;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Livewire\Livewire;

test('the widget asks whether the page helped', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->assertSee('Apakah halaman ini membantu?')
        ->assertSet('submitted', false);
});

test('records a helpful vote', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->call('rate', true)
        ->assertSet('submitted', true)
        ->assertSet('helpful', true)
        ->assertSet('helpfulCount', 1)
        ->assertSet('totalCount', 1);

    $feedback = $page->feedback()->firstOrFail();

    expect($feedback->helpful)->toBeTrue()
        ->and($feedback->visitor_hash)->toHaveLength(64);
});

test('records an unhelpful vote', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->call('rate', false)
        ->assertSet('helpfulCount', 0)
        ->assertSet('totalCount', 1);

    expect($page->feedback()->firstOrFail()->helpful)->toBeFalse();
});

test('changes the visitor vote instead of adding a second row', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    $component = Livewire::test('docs.feedback', ['pageId' => $page->id])->call('rate', true);

    $component->call('rate', false)
        ->assertSet('helpful', false)
        ->assertSet('helpfulCount', 0)
        ->assertSet('totalCount', 1);

    expect($page->feedback()->count())->toBe(1)
        ->and($page->feedback()->firstOrFail()->helpful)->toBeFalse();
});

test('restores the vote of a returning visitor', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])->call('rate', true);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->assertSet('submitted', true)
        ->assertSet('helpful', true);
});

test('stores an optional comment and trims it', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->call('rate', true)
        ->set('comment', '  Tambahkan contoh kode.  ')
        ->call('submit')
        ->assertHasNoErrors();

    expect($page->feedback()->firstOrFail()->comment)->toBe('Tambahkan contoh kode.');
});

test('rejects a comment longer than a thousand characters', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->call('rate', true)
        ->set('comment', str_repeat('a', 1001))
        ->call('submit')
        ->assertHasErrors('comment');
});

test('requires a vote before a comment can be submitted', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->call('submit')
        ->assertHasErrors('helpful');

    expect($page->feedback()->count())->toBe(0);
});

test('renders the aggregate of helpful votes', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    DocFeedback::factory()->create(['doc_page_id' => $page->id, 'helpful' => true, 'visitor_hash' => str_repeat('a', 64)]);
    DocFeedback::factory()->create(['doc_page_id' => $page->id, 'helpful' => false, 'visitor_hash' => str_repeat('b', 64)]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->assertSee('orang merasa halaman ini membantu')
        ->assertSeeHtml('>1</span>')
        ->assertSeeHtml('>2</span>');
});

test('hides the aggregate while nobody has voted', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    Livewire::test('docs.feedback', ['pageId' => $page->id])
        ->assertDontSee('orang merasa halaman ini membantu');
});

test('allows only one vote per visitor and page', function () {
    $product = Product::factory()->create();
    $version = $product->versions()->firstOrFail();
    $page = DocPage::factory()->published()->create(['version_id' => $version->id]);

    DocFeedback::factory()->create(['doc_page_id' => $page->id, 'visitor_hash' => str_repeat('c', 64)]);

    expect(fn () => DocFeedback::factory()->create(['doc_page_id' => $page->id, 'visitor_hash' => str_repeat('c', 64)]))
        ->toThrow(QueryException::class);
});

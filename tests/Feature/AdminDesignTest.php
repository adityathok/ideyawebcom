<?php

use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

test('applies the rootly design system to the admin screens', function () {
    $components = [
        'pages::admin.posts.index',
        'pages::admin.post-form.index',
        'pages::admin.categories.index',
        'pages::admin.tags.index',
        'pages::admin.settings.index',
    ];

    foreach ($components as $component) {
        // Blue tint panels and hairline borders (DESIGN.md).
        Livewire::test($component)
            ->assertSee('fafbff', false)
            ->assertSee('e3eaff', false);

        // The old cream/off-white palette is gone.
        Livewire::test($component)
            ->assertDontSee('f5f1ec', false)
            ->assertDontSee('ebe7e1', false)
            ->assertDontSee('d3cec6', false)
            ->assertDontSee('626260', false)
            ->assertDontSee('52525b', false);
    }
});

test('uses the blue 500 fallback for the default category color', function () {
    Livewire::test('pages::admin.categories.index')
        ->call('create')
        ->assertSet('color', '#7d95ff');
});

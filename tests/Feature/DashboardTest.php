<?php

use App\Models\User;

test('guests are redirected to the login page', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
});

test('applies the rootly design system to the dashboard', function () {
    $this->actingAs(User::factory()->create());

    $response = $this->get(route('dashboard'))->assertOk();

    // Deep-blue inline links, blue tint chrome, and hairline borders (DESIGN.md).
    $response->assertSee('0a1589', false);
    $response->assertSee('fafbff', false);
    $response->assertSee('e3eaff', false);
    $response->assertSee('100f12', false);

    // The old cream/off-white palette is gone.
    $response->assertDontSee('f5f1ec', false);
    $response->assertDontSee('ebe7e1', false);
});

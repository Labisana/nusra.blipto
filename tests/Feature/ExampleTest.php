<?php

use App\Models\User;

test('guests are redirected to the login page from the home route', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});

test('authenticated users are redirected to the dashboard from the home route', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertRedirect(route('dashboard'));
});

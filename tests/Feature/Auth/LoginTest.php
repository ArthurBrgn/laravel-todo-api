<?php

declare(strict_types=1);

use App\Models\User;

test('login successfully', function () {
    $user = User::factory()->create(['password' => 'password']);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertJsonStructure(['token']);
});

test('login wrong password', function () {
    $user = User::factory()->create(['password' => 'password']);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertUnauthorized()
        ->assertExactJson(['error' => trans('auth.failed')]);
});

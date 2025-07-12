<?php

declare(strict_types=1);

use App\Models\User;

test('register successfully', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertCreated()
        ->assertJsonIsObject()
        ->assertJsonStructure(['token'])
        ->assertJsonPath('user.email', 'test@test.com');
});

test('register with errors', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Te',
        'email' => 'test',
        'password' => 'password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('user already exists', function () {
    User::factory()->create(['email' => 'test@test.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test user',
        'email' => 'test@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

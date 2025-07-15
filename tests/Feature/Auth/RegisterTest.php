<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

test('register successfully', function () {
    $payload = [
        'name' => 'Test User',
        'email' => 'test@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ];

    $response = $this->postJson(route('auth.register'), $payload);

    $response->assertCreated()
        ->assertJsonIsObject()
        ->assertJson(
            fn (AssertableJson $json) => $json->hasAll(['token', 'user'])
                ->where('user.name', $payload['name'])
                ->where('user.email', $payload['email'])
        );
});

test('register with errors', function () {
    $response = $this->postJson(route('auth.register'), [
        'name' => 'Te',
        'email' => 'test',
        'password' => 'password',
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['name', 'email', 'password']);
});

test('user already exists', function () {
    User::factory()->create(['email' => 'test@test.com']);

    $response = $this->postJson(route('auth.register'), [
        'name' => 'Test user',
        'email' => 'test@test.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['email']);
});

<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->password = 'password123';
    $this->user = User::factory()->create([
        'password' => Hash::make($this->password),
    ]);
});

test('user can log in successfully and receives a valid token', function () {
    $response = $this->postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => $this->password,
    ]);

    $response->assertOk()
        ->assertJsonStructure(['token'])
        ->assertJson(
            fn ($json) => $json->whereType('token', 'string')
                ->where('token', fn ($token) => ! empty($token))
        );
});

test('user cannot log in with wrong password', function () {
    $response = $this->postJson(route('auth.login'), [
        'email' => $this->user->email,
        'password' => 'wrong-password',
    ]);

    $response->assertUnauthorized()
        ->assertExactJson(['error' => trans('auth.failed')]);
});

test('user cannot log in with unknown email', function () {
    $response = $this->postJson(route('auth.login'), [
        'email' => 'unknown@example.com',
        'password' => 'whatever',
    ]);

    $response->assertUnauthorized()
        ->assertExactJson(['error' => trans('auth.failed')]);
});

test('login fails when missing email or password', function () {
    $this->postJson(route('auth.login'))
        ->assertUnprocessable()
        ->assertInvalid(['email', 'password']);
});

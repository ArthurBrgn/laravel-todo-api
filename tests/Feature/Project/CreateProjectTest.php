<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\User;
use App\Notifications\AssociatedToProjectNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = $this->authenticateUser();

	Notification::fake();
});

test('create a project successfully', function () {
    $users = User::factory(5)->create();

    $payload = [
        'name' => 'New project',
        'description' => 'A detailed description.',
        'user_ids' => $users->pluck('id')->toArray(),
    ];

    $response = $this->postJson(route('projects.store'), $payload)
        ->assertCreated()
        ->assertJson(
            fn (AssertableJson $json) => $json->where('name', $payload['name'])
                ->where('description', $payload['description'])
                ->etc()
        );

    $this->assertDatabaseHas('projects', [
        'name' => $payload['name'],
        'description' => $payload['description'],
    ]);

    $project = Project::with('users')->findOrFail($response->json('id'));

    expect($project->users)->toHaveCount(5);

	Notification::assertSentTo($users, AssociatedToProjectNotification::class);
	Notification::assertCount(5);
});

test('fail name is missing', function () {
    $this->postJson(route('projects.store'))
        ->assertUnprocessable()
        ->assertOnlyInvalid(['name']);

	Notification::assertNothingSent();
});

test('fail user_ids duplicate', function () {
    $this->postJson(route('projects.store'), [
        'name' => 'New project',
        'user_ids' => [1, 1],
    ])
        ->assertUnprocessable()
        ->assertOnlyInvalid(['user_ids.0', 'user_ids.1']);

	Notification::assertNothingSent();
});

test('fail user_id not exists', function () {
    $this->postJson(route('projects.store'), [
        'name' => 'New project',
        'user_ids' => [999],
    ])
        ->assertUnprocessable()
        ->assertOnlyInvalid(['user_ids.0']);

	Notification::assertNothingSent();
});

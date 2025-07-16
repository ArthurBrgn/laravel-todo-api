<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Notifications\AssociatedToProjectNotification;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    Notification::fake();
});

test('add user to project successfully', function () {
    $project = Project::factory()->create();

    $response = $this->postJson(route('projects.user.associate', [$project, $this->user]));

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertExactJson(
            (new UserResource($this->user))->resolve()
        );

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $this->user->id,
    ]);

    Notification::assertSentTo($this->user, AssociatedToProjectNotification::class);
    Notification::assertCount(1);
});

test('user already present', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $response = $this->postJson(route('projects.user.associate', [$project, $this->user]));

    $response->assertStatus(Response::HTTP_CONFLICT)
        ->assertExactJson(
            ['error' => 'Cet utilisateur est déjà présent dans le projet.']
        );

    Notification::assertNothingSent();
});

test('project doesn\'t exists', function () {
    $response = $this->postJson("/api/projects/999/users/{$this->user->id}/associate");

    $response->assertNotFound();

    $this->assertDatabaseEmpty('project_user');

    Notification::assertNothingSent();
});

test('user doesn\'t exists', function () {
    $project = Project::factory()->create();

    $response = $this->postJson(route('projects.user.associate', [$project, 999]));

    $response->assertNotFound();

    $this->assertDatabaseEmpty('project_user');

    Notification::assertNothingSent();
});

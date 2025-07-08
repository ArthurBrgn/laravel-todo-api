<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

test('add user to project successfully', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/{$user->id}/associate");

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertExactJson(
            (new UserResource($user))->resolve()
        );

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $user->id,
    ]);
});

test('user already present', function () {
    $user = User::factory()->create();
    $project = Project::factory()->hasAttached($user)->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/{$user->id}/associate");

    $response->assertStatus(Response::HTTP_CONFLICT)
        ->assertExactJson(
            ['error' => 'Cet utilisateur est déjà présent dans le projet.']
        );
});

test('project doesn\'t exists', function () {
    $user = User::factory()->create();

    $response = $this->postJson("/api/projects/999/users/{$user->id}/associate");

    $response->assertNotFound();
});

test('user doesn\'t exists', function () {
    $project = Project::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/999/associate");

    $response->assertNotFound();
});

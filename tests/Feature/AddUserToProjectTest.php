<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\Project;
use Symfony\Component\HttpFoundation\Response;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('add user to project successfully', function () {
    $project = Project::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/{$this->user->id}/associate");

    $response->assertOk()
        ->assertJsonIsObject()
        ->assertExactJson(
            (new UserResource($this->user))->resolve()
        );

    $this->assertDatabaseHas('project_user', [
        'project_id' => $project->id,
        'user_id' => $this->user->id,
    ]);
});

test('user already present', function () {
    $project = Project::factory()->hasAttached($this->user)->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/{$this->user->id}/associate");

    $response->assertStatus(Response::HTTP_CONFLICT)
        ->assertExactJson(
            ['error' => 'Cet utilisateur est déjà présent dans le projet.']
        );
});

test('project doesn\'t exists', function () {
    $response = $this->postJson("/api/projects/999/users/{$this->user->id}/associate");

    $response->assertNotFound();
});

test('user doesn\'t exists', function () {
    $project = Project::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/999/associate");

    $response->assertNotFound();
});

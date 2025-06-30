<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use App\Models\Project;
use App\Models\User;

test('add user to project successfully', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/users/{$user->id}/add");

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

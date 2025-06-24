<?php

declare(strict_types=1);

use App\Models\Project;

test('create tag successfully', function () {
    $project = Project::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/tags", [
        'name' => 'New Tag',
    ]);

    $response->assertCreated()
        ->assertJson([
            'name' => 'New Tag',
        ]);
});

test('create tag with existing name fails', function () {
    $project = Project::factory()->create();
    $project->tags()->create(['name' => 'Existing Tag']);

    $response = $this->postJson("/api/projects/{$project->id}/tags", [
        'name' => 'Existing Tag',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

test('create tag with short name fails', function () {
    $project = Project::factory()->create();

    $response = $this->postJson("/api/projects/{$project->id}/tags", [
        'name' => 'ab',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});
<?php

declare(strict_types=1);

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;

test('get tag list', function () {
    $project = Project::factory()
        ->hasTags(30) // Create 15 tags for the project
        ->create();

    $response = $this->getJson("/api/projects/{$project->id}/tags");

    $response
        ->assertOk()
        ->assertJsonIsObject()
        ->assertJson(fn (AssertableJson $json) => $json->has('meta')
            ->has('links')
            ->has('data', 15)
        );
});

test('project not found when getting tags', function () {
    $response = $this->getJson('/api/projects/999/tags');

    $response->assertNotFound();
});

<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Tag;

test('search tags successfully', function () {
    $project = Project::factory()->create();

    $tag1 = Tag::factory()->for($project)->create(['name' => 'Test Tag 1']);
    Tag::factory()->for($project)->create(['name' => 'Another Tag']);

    $response = $this->getJson('/api/projects/'.$project->id.'/tags/search?searchTerm=Test');

    $response
        ->assertOk()
        ->assertJsonIsObject()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $tag1->id, 'name' => 'Test Tag 1']);
});

test('search term not present', function () {
    $project = Project::factory()->create();

    $response = $this->getJson('/api/projects/'.$project->id.'/tags/search');

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['searchTerm']);
});

test('search term too short', function () {
    $project = Project::factory()->create();

    $response = $this->getJson('/api/projects/'.$project->id.'/tags/search?searchTerm=X');

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['searchTerm']);
});

test('project not found', function () {
    $response = $this->getJson('/api/projects/999999/tags/search?searchTerm=Test');

    $response->assertNotFound();
});

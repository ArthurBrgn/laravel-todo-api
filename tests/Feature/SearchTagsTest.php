<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Tag;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('search tags successfully', function () {
    $project = Project::factory()->create();

    $tag1 = Tag::factory()->for($project)->create(['name' => 'Test Tag 1']);
    Tag::factory()->for($project)->create(['name' => 'Another Tag']);

    $response = $this->getJson('/api/projects/'.$project->id.'/tags/search?search=Test');

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
        ->assertJsonValidationErrors(['search']);
});

test('search term too short', function () {
    $project = Project::factory()->create();

    $response = $this->getJson('/api/projects/'.$project->id.'/tags/search?search=X');

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['search']);
});

test('project not found', function () {
    $response = $this->getJson('/api/projects/999999/tags/search?search=Test');

    $response->assertNotFound();
});

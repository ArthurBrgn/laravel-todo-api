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

    $response = $this->getJson(route('tags.search', [$project, 'search' => 'Test']));

    $response
        ->assertOk()
        ->assertJsonIsObject()
        ->assertJsonCount(1, 'data')
        ->assertJsonFragment(['id' => $tag1->id, 'name' => 'Test Tag 1']);
});

test('search term not present', function () {
    $project = Project::factory()->create();

    $response = $this->getJson(route('tags.search', $project));

    $response
        ->assertUnprocessable()
        ->assertOnlyInvalid(['search']);
});

test('search term too short', function () {
    $project = Project::factory()->create();

    $response = $this->getJson(route('tags.search', [$project, 'search' => 'X']));

    $response
        ->assertUnprocessable()
        ->assertOnlyInvalid(['search']);
});

test('project not found', function () {
    $this->getJson(route('tags.search', [999, 'search' => 'Test']))
        ->assertNotFound();
});

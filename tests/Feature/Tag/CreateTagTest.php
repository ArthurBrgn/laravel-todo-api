<?php

declare(strict_types=1);

use App\Models\Project;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('create tag successfully', function () {
    $project = Project::factory()->create();

    $payload = ['name' => 'New Tag'];

    $response = $this->postJson(route('projects.tags.store', $project), $payload);

    $response->assertCreated()
        ->assertJsonIsObject()
        ->assertJson([
            'name' => $payload['name'],
        ]);

    $this->assertDatabaseHas('tags', [
        'name' => $payload['name'],
    ]);
});

test('create tag with existing name fails', function () {
    $project = Project::factory()->create();
    $project->tags()->create(['name' => 'Existing Tag']);

    $response = $this->postJson(route('projects.tags.store', $project), [
        'name' => 'Existing Tag',
    ]);

    $response->assertUnprocessable()
        ->assertJsonIsObject()
        ->assertOnlyInvalid(['name']);
});

test('create tag with short name fails', function () {
    $project = Project::factory()->create();

    $response = $this->postJson(route('projects.tags.store', $project), [
        'name' => 'ab',
    ]);

    $response->assertUnprocessable()
        ->assertJsonIsObject()
        ->assertOnlyInvalid(['name']);
});

test('project not found when creating tag', function () {
    $response = $this->postJson(route('projects.tags.store', '999'), [
        'name' => 'New Tag',
    ]);

    $response->assertNotFound();
});

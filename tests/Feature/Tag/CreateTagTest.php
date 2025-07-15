<?php

declare(strict_types=1);

use App\Models\Project;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('create tag successfully', function () {
    $project = Project::factory()->create();

    $response = $this->postJson(route('projects.tags.store', $project), [
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

    $response = $this->postJson(route('projects.tags.store', $project), [
        'name' => 'Existing Tag',
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['name']);
});

test('create tag with short name fails', function () {
    $project = Project::factory()->create();

    $response = $this->postJson(route('projects.tags.store', $project), [
        'name' => 'ab',
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['name']);
});

test('project not found when creating tag', function () {
    $response = $this->postJson(route('projects.tags.store', '999'), [
        'name' => 'New Tag',
    ]);

    $response->assertNotFound();
});

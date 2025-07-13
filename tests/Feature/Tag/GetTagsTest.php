<?php

declare(strict_types=1);

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('get tag list', function () {
    $project = Project::factory()
        ->hasTags(30)
        ->create();

    $response = $this->getJson(route('projects.tags.index', $project));

    $response
        ->assertOk()
        ->assertJsonIsObject()
        ->assertJson(
            fn (AssertableJson $json) => $json->has('meta')
                ->has('links')
                ->has('data', 15)
        );
});

test('project not found when getting tags', function () {
    $response = $this->getJson('/api/projects/999/tags');

    $response->assertNotFound();
});

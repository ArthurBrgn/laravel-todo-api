<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Tag;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('update tag successfully', function () {
    $tag = Tag::factory()
        ->for(Project::factory())
        ->create(['name' => 'Old Tag']);

    $response = $this->patchJson(route('tags.update', $tag), [
        'name' => 'Updated Tag',
    ]);

    $response->assertOk()
        ->assertJson([
            'name' => 'Updated Tag',
        ]);
});

test('update tag with existing name fails', function () {
    $project = Project::factory()->create();

    $tag1 = Tag::factory()
        ->for($project)
        ->create(['name' => 'Tag One']);

    Tag::factory()
        ->for($project)
        ->create(['name' => 'Tag Two']);

    $response = $this->patchJson(route('tags.update', $tag1), [
        'name' => 'Tag Two',
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['name']);
});

test('tag not found when updating', function () {
    $response = $this->patchJson(route('tags.update', 999), [
        'name' => 'Updated Tag',
    ]);

    $response->assertNotFound();
});

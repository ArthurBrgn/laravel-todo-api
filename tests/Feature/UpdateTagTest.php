<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Tag;

test('update tag successfully', function () {
    $tag = Tag::factory()
        ->for(Project::factory())
        ->create(['name' => 'Old Tag']);

    $response = $this->patchJson("/api/tags/{$tag->id}", [
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

    $response = $this->patchJson("/api/tags/{$tag1->id}", [
        'name' => 'Tag Two',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);
});

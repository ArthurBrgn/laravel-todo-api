<?php

declare(strict_types=1);

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;

test('get project list', function () {
    Project::factory(40)->create();

    $response = $this->getJson('/api/projects');

    $response->assertStatus(200);

    $response
        ->assertJsonIsObject()
        ->assertJson(fn (AssertableJson $json) => $json->has('meta')
            ->has('links')
            ->has('data', 20)
        );
});

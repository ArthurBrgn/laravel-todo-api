<?php

declare(strict_types=1);

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;

test('get project list', function () {
    Project::factory(20)->create();

    $response = $this->getJson('/api/projects');

    $response
        ->assertOk()
        ->assertJsonIsObject()
        ->assertJson(fn (AssertableJson $json) => $json->has('meta')
            ->has('links')
            ->has('data', 15)
        );
});

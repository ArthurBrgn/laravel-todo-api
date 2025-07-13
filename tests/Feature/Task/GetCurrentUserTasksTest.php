<?php

declare(strict_types=1);

use App\Models\Project;
use App\Models\Task;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('get current user tasks', function () {
    $project = Project::factory()->create();

    Task::factory(10)
        ->for($project)
        ->for($this->user, 'createdBy')
        ->for($this->user, 'assignedTo')
        ->create();

    $response = $this->getJson(route('tasks.user.current'));

    $response
        ->assertOk()
        ->assertJsonIsObject()
        ->assertJson(
            fn (AssertableJson $json) => $json->has('meta')
                ->has('links')
                ->has('data', 10)
        );
});

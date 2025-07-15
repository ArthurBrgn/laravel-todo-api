<?php

declare(strict_types=1);

use App\Enum\TaskPoints;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    $this->project = Project::factory()->hasAttached($this->user)->create();
});

test('create a task successfully', function () {
    $tags = Tag::factory()->for($this->project)->count(2)->create();
    $assignedUser = User::factory()->create();

    $payload = [
        'name' => 'New Task',
        'description' => 'A detailed description.',
        'points' => TaskPoints::FIVE->value,
        'tag_ids' => $tags->pluck('id')->toArray(),
        'assigned_to_id' => $assignedUser->id,
    ];

    $this->postJson(route('tasks.store', $this->project), $payload)
        ->assertCreated()
        ->assertJson(
            fn (AssertableJson $json) => $json->where('name', $payload['name'])
                ->where('description', $payload['description'])
                ->where('points', $payload['points'])
                ->etc()
        );
});

test('fail name is missing', function () {
    $this->postJson(route('tasks.store', $this->project), [
        // 'name' => 'missing',
        'points' => TaskPoints::THREE->value,
    ])->assertOnlyInvalid(['name']);
});

test('fail name too short', function () {
    $this->postJson(route('tasks.store', $this->project), [
        'name' => 'ab',
        'points' => TaskPoints::ONE->value,
    ])->assertOnlyInvalid(['name']);
});

test('fails description too short', function () {
    $this->postJson(route('tasks.store', $this->project), [
        'name' => 'Valid name',
        'description' => 'ab',
        'points' => TaskPoints::ONE->value,
    ])->assertOnlyInvalid(['description']);
});

test('fails points invalid', function () {
    $this->postJson(route('tasks.store', $this->project), [
        'name' => 'Valid Task',
        'points' => 'invalid_enum',
    ])->assertOnlyInvalid(['points']);
});

test('fail tag id not exists', function () {
    $this->postJson(route('tasks.store', $this->project), [
        'name' => 'Valid Task',
        'points' => TaskPoints::ONE->value,
        'tag_ids' => [9999],
    ])->assertOnlyInvalid(['tag_ids.0']);
});

test('fail assigned_to_id does not exist', function () {
    $this->postJson(route('tasks.store', $this->project), [
        'name' => 'Valid Task',
        'points' => TaskPoints::ONE->value,
        'assigned_to_id' => 9999,
    ])->assertOnlyInvalid(['assigned_to_id']);
});

test('fail parent_id does not exist', function () {
    $this->postJson(route('tasks.store', $this->project), [
        'name' => 'Valid Task',
        'points' => TaskPoints::ONE->value,
        'parent_id' => 9999,
    ])->assertOnlyInvalid(['parent_id']);
});

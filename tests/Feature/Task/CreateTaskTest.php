<?php

declare(strict_types=1);

use App\Enum\TaskPoints;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = $this->authenticateUser();

    $this->project = Project::factory()->create();
});

it('creates a task successfully with full payload', function () {
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

// it('fails when name is missing', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         // 'name' => 'missing',
//         'points' => TaskPoints::THREE->value,
//     ])->assertInvalid(['name']);
// });

// it('fails when name is too short', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'ab',
//         'points' => TaskPoints::ONE->value,
//     ])->assertInvalid(['name']);
// });

// it('fails when description is too short', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'Valid name',
//         'description' => 'ab',
//         'points' => TaskPoints::ONE->value,
//     ])->assertInvalid(['description']);
// });

// it('fails when points is not a valid enum', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'Valid Task',
//         'points' => 'invalid_enum',
//     ])->assertInvalid(['points']);
// });

// it('fails when tag_ids contain non-existent tag', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'Valid Task',
//         'points' => TaskPoints::ONE->value,
//         'tag_ids' => [9999], // Tag doesn't exist
//     ])->assertInvalid(['tag_ids.0']);
// });

// it('fails when assigned_to_id does not exist', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'Valid Task',
//         'points' => TaskPoints::ONE->value,
//         'assigned_to_id' => 9999,
//     ])->assertInvalid(['assigned_to_id']);
// });

// it('fails when parent_id does not exist', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'Valid Task',
//         'points' => TaskPoints::ONE->value,
//         'parent_id' => 9999,
//     ])->assertInvalid(['parent_id']);
// });

// it('creates a task with minimal valid payload', function () {
//     $this->postJson(route('tasks.store', $this->project), [
//         'name' => 'Minimal Task',
//         'points' => TaskPoints::ONE->value,
//     ])
//         ->assertCreated()
//         ->assertJson(fn (AssertableJson $json) =>
//             $json->where('data.name', 'Minimal Task')
//                 ->where('data.points', TaskPoints::ONE->value)
//                 ->etc()
//         );
// });

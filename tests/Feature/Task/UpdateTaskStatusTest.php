<?php

declare(strict_types=1);

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Response;
use Illuminate\Testing\Fluent\AssertableJson;

beforeEach(function () {
    $this->user = $this->authenticateUser();
});

test('update task status successfully', function () {
    $cases = TaskStatus::cases();

    foreach ($cases as $case) {
        foreach ($case->allowedTransitions() as $newStatus) {
            $project = Project::factory()->hasAttached($this->user)->create();

            $task = Task::factory()
                ->for($project)
                ->for($this->user, 'createdBy')
                ->create(['status' => $case]);

            $response = $this->patchJson(route('task.status.update', $task), [
                'status' => $newStatus->value,
            ]);

            $response->assertOk()
                ->assertJsonIsObject()
                ->assertJson(
                    fn (AssertableJson $json) => $json->where('status', $newStatus->value)
                        ->where('name', $task->name)
                        ->where('number', $task->number)
                        ->where('description', $task->description)
                        ->where('points', $task->points)
                        ->etc()
                );
        }
    }
});

test('user authorized', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create(['status' => TaskStatus::TODO]);

    $response = $this->patchJson(route('task.status.update', $task), [
        'status' => TaskStatus::DOING->value,
    ]);

    $response->assertForbidden();

    $this->assertDatabaseMissing('tasks', [
        'id' => $task->id,
        'status' => TaskStatus::DOING->value,
    ]);
});

test('status does not exists', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->patchJson(route('task.status.update', $task), [
        'status' => 'test',
    ]);

    $response->assertUnprocessable()
        ->assertOnlyInvalid(['status']);
});

test('cannot transition to new status', function () {
    $cases = TaskStatus::cases();

    foreach ($cases as $case) {
        $allowedTransitions = $case->allowedTransitions();

        $newStatusesToTest = \array_udiff($cases, [...$allowedTransitions, $case], function (TaskStatus $a, TaskStatus $b) {
            return $a->value <=> $b->value;
        });

        foreach ($newStatusesToTest as $newStatus) {
            $project = Project::factory()->hasAttached($this->user)->create();

            $task = Task::factory()
                ->for($project)
                ->for($this->user, 'createdBy')
                ->create(['status' => $case]);

            $response = $this->patchJson(route('task.status.update', $task), [
                'status' => $newStatus->value,
            ]);

            $response->assertStatus(Response::HTTP_CONFLICT)
                ->assertExactJson(
                    ['error' => "Transtion du statut {$task->status->value} à {$newStatus->value} impossible"]
                );

            $this->assertDatabaseMissing('tasks', [
                'id' => $task->id,
                'status' => $newStatus->value,
            ]);
        }
    }
});

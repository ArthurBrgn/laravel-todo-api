<?php

declare(strict_types=1);

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Response;

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

            $newStatus = $newStatus->value;

            $response = $this->patchJson("/api/tasks/{$task->id}/status", [
                'status' => $newStatus,
            ]);

            $response->assertOk()
                ->assertJsonIsObject()
                ->assertJson([
                    'status' => $newStatus,
                ]);
        }
    }
});

test('user authorized', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create(['status' => TaskStatus::TODO]);

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => TaskStatus::DOING->value,
    ]);

    $response->assertForbidden();
});

test('status does not exists', function () {
    $project = Project::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($this->user, 'createdBy')
        ->create();

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => 'test',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
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

            $response = $this->patchJson("/api/tasks/{$task->id}/status", [
                'status' => $newStatus->value,
            ]);

            $response->assertStatus(Response::HTTP_CONFLICT)
                ->assertExactJson(
                    ['error' => "Transtion du statut {$task->status->value} à {$newStatus->value} impossible"]
                );
        }
    }
});

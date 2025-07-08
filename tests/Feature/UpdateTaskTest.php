<?php

declare(strict_types=1);

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Response;

test('update task status successfully', function () {
    $cases = TaskStatus::cases();

    foreach ($cases as $case) {
        foreach ($case->allowedTransitions() as $newStatus) {
            $project = Project::factory()->create();
            $user = User::factory()->create();

            $task = Task::factory()
                ->for($project)
                ->for($user, 'createdBy')
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

test('status does not exists', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($user, 'createdBy')
        ->create();

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => 'test',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['status']);
});

test('cannot transition', function () {
    $project = Project::factory()->create();
    $user = User::factory()->create();

    $task = Task::factory()
        ->for($project)
        ->for($user, 'createdBy')
        ->create();

    $newStatus = TaskStatus::DONE->value;

    $response = $this->patchJson("/api/tasks/{$task->id}/status", [
        'status' => $newStatus,
    ]);

    $response->assertStatus(Response::HTTP_CONFLICT)
        ->assertExactJson(
            ['error' => "Transtion du statut {$task->status->value} à {$newStatus} impossible"]
        );
});

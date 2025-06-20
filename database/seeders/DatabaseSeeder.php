<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create a main user for testing
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create 10 random users
        $users = User::factory(50)->create();

        // Create 5 projects
        Project::factory(5)
            ->hasTags(rand(5, 10))
            ->create()
            ->each(function (Project $project) use ($users) {
                $projectUsers = $users->random(rand(3, 10));

                // Attach 1 to 3 random users to each project
                $project->users()->attach($projectUsers);

                $tags = $project->tags;

                // Create 5 to 10 tasks for each project
                Task::factory(rand(5, 10))
                    ->hasComments(rand(0, 3), [
                        'user_id' => $projectUsers->random(),
                    ])
                    ->create(['project_id' => $project->id, 'created_by' => $projectUsers->random()])
                    ->each(function (Task $task) use ($projectUsers, $tags) {
                        // Attach 1 to 3 random tags to each task
                        $task->tags()->attach(
                            $tags->random(rand(1, 3))->pluck('id')
                        );

                        // 50% chance to create sub-tasks
                        if (rand(0, 1)) {
                            Task::factory(rand(1, 3))
                                ->hasComments(rand(0, 2), [
                                    'user_id' => $projectUsers->random(),
                                ])
                                ->create([
                                    'project_id' => $task->project_id,
                                    'parent_id' => $task->id,
                                    'created_by' => $projectUsers->random(),
                                ])
                                ->each(function ($subtask) use ($tags) {
                                    // Attach 1 to 3 random tags to each sub-task
                                    $subtask->tags()->attach(
                                        $tags->random(rand(1, 3))
                                    );
                                });
                        }
                    });
            });
    }
}

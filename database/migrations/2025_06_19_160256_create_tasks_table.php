<?php

declare(strict_types=1);

use App\Enum\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('status', TaskStatus::values())->default(TaskStatus::TODO->value);

            $table->foreignIdFor(Project::class)
                ->constrained()
                ->onDelete('cascade');

            $table->foreignIdFor(Task::class, 'parent_id')
                ->nullable()
                ->constrained('tasks')
                ->onDelete('cascade');

            $table->foreignIdFor(User::class, 'created_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignIdFor(User::class, 'assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};

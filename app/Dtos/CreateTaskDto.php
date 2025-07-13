<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Enum\TaskPoints;
use App\Http\Requests\CreateTaskRequest;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class CreateTaskDto
{
    public function __construct(
        public string $name,
        public ?string $description,
        public TaskPoints $points,
        public Collection $tags,
        public ?Task $parent,
        public ?User $assignedTo
    ) {}

    public static function fromRequest(CreateTaskRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            points: TaskPoints::from($request->validated('points')),
            tags: Tag::whereIn('id', $request->validated('tag_ids', []))->get(),
            parent: $request->validated('parent_id') ? Task::find($request->validated('parent_id')) : null,
            assignedTo: $request->validated('assigned_to_id') ? User::find($request->validated('assigned_to_id')) : null
        );
    }
}

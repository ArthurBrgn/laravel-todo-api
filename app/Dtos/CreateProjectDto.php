<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Http\Requests\CreateProjectRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class CreateProjectDto
{
    public function __construct(
        public string $name,
        public ?string $description,
        public Collection $users
    ) {}

    public static function fromRequest(CreateProjectRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            description: $request->validated('description'),
            users: User::findMany($request->validated('user_ids') ?? [])
        );
    }
}

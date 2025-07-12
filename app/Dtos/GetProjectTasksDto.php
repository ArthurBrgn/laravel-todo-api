<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Http\Requests\GetProjectTasksRequest;

final readonly class GetProjectTasksDto
{
    public function __construct(
        public array $userIds = [],
        public array $tagIds = []
    ) {}

    public static function fromRequest(GetProjectTasksRequest $request): self
    {
        return new self(
            userIds: $request->validated('user_ids', []),
            tagIds: $request->validated('tag_ids', [])
        );
    }
}

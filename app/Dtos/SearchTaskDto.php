<?php

declare(strict_types=1);

namespace App\Dtos;

use App\Http\Requests\SearchTaskRequest;

final readonly class SearchTaskDto
{
    public function __construct(
        public string $search,
        public ?int $projectId,
        public array $tagIds = []
    ) {}

    public static function fromRequest(SearchTaskRequest $request): self
    {
        return new self(
            search: $request->validated('search'),
            projectId: $request->validated('project_id'),
            tagIds: $request->validated('tag_ids', [])
        );
    }
}

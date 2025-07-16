<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;

final readonly class FilterTasksQuery
{
    public function __construct(private array $userIds = [], private array $tagIds = []) {}

    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public function __invoke(Builder $query): Builder
    {
        return $query
            ->with(['createdBy', 'assignedTo'])
            ->withCount('subTasks')
            ->onlyParents()
            ->when(! empty($this->userIds), function (Builder $query) {
                return $query->whereIn('assigned_to_id', $this->userIds);
            })
            ->when(! empty($this->tagIds), function (Builder $query) {
                return $query->whereHas('tags', fn (Builder $query) => $query->whereIn('tags.id', $this->tagIds));
            });
    }
}

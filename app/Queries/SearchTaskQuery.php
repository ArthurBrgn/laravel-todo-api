<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;

final class SearchTaskQuery
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public static function handle(Builder $query, string $searchTerm, ?int $projectId = null): Builder
    {
        return $query
            ->with(['createdBy', 'assignedTo'])
            ->withCount('subTasks')
            ->when($projectId, function ($query) use ($projectId) {
                return $query->where('project_id', $projectId);
            })
            ->whereLike('name', "%$searchTerm%")
            ->orWhereLike('description', "%$searchTerm%")
            ->orWhereLike('number', "%$searchTerm%");
    }
}

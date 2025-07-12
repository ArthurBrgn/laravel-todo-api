<?php

declare(strict_types=1);

namespace App\Queries;

use App\Dtos\GetProjectTasksDto;
use Illuminate\Database\Eloquent\Builder;

final class GetProjectTasksQuery
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public static function handle(Builder $query, GetProjectTasksDto $data): Builder
    {
        $userIds = $data->userIds;
        $tagIds = $data->tagIds;

        return $query
            ->with(['createdBy', 'assignedTo'])
            ->withCount('subTasks')
            ->onlyParents()
            ->when(! empty($userIds), function (Builder $query) use ($userIds) {
                return $query->whereIn('assigned_to_id', $userIds);
            })
            ->when(! empty($tagIds), function (Builder $query) use ($tagIds) {
                return $query->whereHas('tags', fn (Builder $query) => $query->whereIn('tags.id', $tagIds));
            });
    }
}

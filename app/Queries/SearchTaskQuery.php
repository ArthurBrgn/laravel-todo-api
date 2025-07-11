<?php

declare(strict_types=1);

namespace App\Queries;

use App\Dtos\SearchTaskDto;
use Illuminate\Database\Eloquent\Builder;

final class SearchTaskQuery
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public static function handle(Builder $query, SearchTaskDto $data): Builder
    {
        $search = $data->search;
        $tagIds = $data->tagIds;

        return $query
            ->with(['assignedTo'])
            ->withCount('subTasks')
            ->when($data->projectId, fn ($query) => $query->where('project_id', $data->projectId))
            ->when(! empty($tagIds), function ($query) use ($tagIds) {
                return $query->whereHas('tags', function ($query) use ($tagIds) {
                    $query->whereIn('tags.id', $tagIds);
                });
            })
            ->where(function ($query) use ($search) {
                $query->whereLike('name', "%$search%")
                    ->orWhereLike('description', "%$search%")
                    ->orWhereLike('number', "%$search%");
            });
    }
}

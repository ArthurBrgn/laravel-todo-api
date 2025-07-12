<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;

final class SearchTaskQuery
{
    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public static function handle(Builder $query, string $search): Builder
    {
        return $query
            ->with(['assignedTo'])
            ->withCount('subTasks')
            ->where(function ($query) use ($search) {
                $query->whereLike('name', "%$search%")
                    ->orWhereLike('description', "%$search%")
                    ->orWhereLike('number', "%$search%");
            });
    }
}

<?php

declare(strict_types=1);

namespace App\Queries;

use Illuminate\Database\Eloquent\Builder;

final readonly class SearchTasksQuery
{
    public function __construct(private string $search) {}

    /**
     * Search for tasks based on a search term and optional project ID.
     */
    public function __invoke(Builder $query): Builder
    {
        return $query
            ->where(function ($query) {
                $query->whereLike('name', "%$this->search%")
                    ->orWhereLike('description', "%$this->search%")
                    ->orWhereLike('number', "%$this->search%");
            });
    }
}

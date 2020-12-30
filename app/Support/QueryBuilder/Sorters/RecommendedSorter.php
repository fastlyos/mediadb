<?php

namespace App\Support\QueryBuilder\Sorters;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class RecommendedSorter implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $descending
     * @param string                                $property
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        // Use given order by filter(s)
        if ($query->getQuery()->orders) {
            return $query;
        }

        return $query->inRandomSeedOrder();
    }
}
<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\Filters\Filter;

class TypeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        return $query
            ->when('user' === $value, fn ($query) => $query->where('model_type', User::class)
                ->where('model_id', Auth::user()->id ?? 0)
            );
    }
}

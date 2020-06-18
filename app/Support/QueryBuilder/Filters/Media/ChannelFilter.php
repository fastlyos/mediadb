<?php

namespace App\Support\QueryBuilder\Filters\Media;

use App\Models\Channel;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class ChannelFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert arrays to string
        $value = is_array($value) ? implode(' ', $value) : $value;

        // Media models
        $ids = Channel::findBySlugOrFail($value)->media->pluck('id')->toArray();

        return $query->whereIn('id', $ids);
    }
}

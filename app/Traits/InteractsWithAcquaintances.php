<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithAcquaintances
{
    /**
     * @return bool
     */
    public function getIsFavoritedAttribute(?User $user = null): bool
    {
        return $this->isFavoritedBy(
            $user ?? auth()->user()
        );
    }

    /**
     * @return bool
     */
    public function getIsSubscribedAttribute(?User $user = null): bool
    {
        return $this->isSubscribedBy(
            $user ?? auth()->user()
        );
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithUserFavorites(Builder $query): Builder
    {
        return $query->whereHas('favoriters', function (Builder $query) {
            $query->where('id', auth()->user()->id);
        });
    }
}

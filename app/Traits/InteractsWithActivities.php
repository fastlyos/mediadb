<?php

namespace App\Traits;

use App\Models\User;

trait InteractsWithActivities
{
    /**
     * @param string    $log
     * @param array     $properties
     * @param User|null $user
     *
     * @return void
     */
    public function recordActivity(
        string $log,
        array $properties = null,
        ?User $user = null
    ): void {
        activity()
            ->performedOn($this)
            ->causedBy($user ?? auth()->user())
            ->withProperties($properties)
            ->log($log);
    }
}

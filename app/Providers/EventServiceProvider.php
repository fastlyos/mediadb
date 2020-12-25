<?php

namespace App\Providers;

use App\Events\Media\HasBeenAdded;
use App\Listeners\CollectionEventSubscriber;
use App\Listeners\Media\Process as ProcessMedia;
use App\Listeners\VideoEventSubscriber;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        HasBeenAdded::class => [
            ProcessMedia::class,
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        CollectionEventSubscriber::class,
        VideoEventSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    }
}

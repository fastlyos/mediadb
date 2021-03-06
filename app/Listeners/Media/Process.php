<?php

namespace App\Listeners\Media;

use App\Events\Media\HasBeenAdded;
use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Jobs\Media\SetMetadata;
use Illuminate\Support\Facades\Bus;

class Process
{
    /**
     * @param HasBeenAdded $event
     *
     * @return void
     */
    public function handle(HasBeenAdded $event): void
    {
        // e.g. video/mp4 => video
        $type = strtok($event->media->mime_type, '/');

        switch ($type) {
            case 'video':
                Bus::chain([
                    new SetMetadata($event->media),
                    new CreateThumbnail($event->media),
                    new CreateSprite($event->media),
                ])->onQueue('media')->dispatch($event->media);
                break;
        }
    }
}

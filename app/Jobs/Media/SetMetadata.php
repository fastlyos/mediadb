<?php

namespace App\Jobs\Media;

use App\Models\Media;
use App\Services\MediaMetadataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetMetadata implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var bool
     */
    public bool $deleteWhenMissingModels = true;

    /**
     * @var int
     */
    public int $tries = 3;

    /**
     * @var int
     */
    public int $timeout = 120;

    /**
     * @var Media
     */
    protected Media $media;

    public function __construct(Media $media)
    {
        $this->media = $media->withoutRelations();
    }

    /**
     * @return void
     */
    public function handle(MediaMetadataService $mediaMetadataService): void
    {
        // e.g. video/mp4 => video
        $type = strtok($this->media->mime_type, '/');

        $path = $this->media->getPath();

        $metadata = $mediaMetadataService->getFormatAttributes($path);

        switch ($type) {
            case 'video':
                $video = $mediaMetadataService->getVideoAttributes($path);

                $metadata = $metadata->merge($video);
                break;
        }

        $this
            ->media
            ->setCustomProperty('metadata', $metadata->all())
            ->save();
    }

    /**
     * @return array
     */
    public function tags(): array
    {
        return ['metadata', 'media:'.$this->media->id];
    }
}

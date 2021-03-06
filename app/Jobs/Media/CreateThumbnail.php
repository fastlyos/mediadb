<?php

namespace App\Jobs\Media;

use App\Models\Media;
use App\Services\MediaThumbnailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateThumbnail implements ShouldQueue
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
    public int $timeout = 300;

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
    public function handle(MediaThumbnailService $mediaThumbnailService): void
    {
        $mediaThumbnailService->create($this->media);
    }

    /**
     * @return array
     */
    public function tags(): array
    {
        return ['thumbnail', 'media:'.$this->media->id];
    }
}

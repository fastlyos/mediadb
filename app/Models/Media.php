<?php

namespace App\Models;

use App\Traits\HasHashids;
use App\Traits\HasViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;
use Spatie\ModelStatus\HasStatuses;

class Media extends BaseMedia implements Viewable
{
    use HasHashids;
    use HasStatuses;
    use HasViews;
    use InteractsWithViews;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this);

        return $urlGenerator->getBasePath();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this);

        return $urlGenerator->getBaseMediaPath();
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return $this->getUrl();
    }

    /**
     * @return string
     */
    public function getLocaleAttribute(): string
    {
        return $this->getCustomProperty('locale', 'undefined');
    }
}

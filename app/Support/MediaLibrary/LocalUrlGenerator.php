<?php

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;

class LocalUrlGenerator extends BaseUrlGenerator
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return '';
    }

    /**
     * @param \DateTimeInterface $expiration
     * @param array              $options
     *
     * @return string
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return URL::temporarySignedRoute(
            'api.media.download',
            $expiration,
            [
                $this->media,
                auth()->user(),
                $this->conversion ? $this->conversion->getName() : 'media',
                $this->media->updated_at->timestamp,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBaseMediaDirectoryUrl()
    {
        return $this->getDisk()->url('/');
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        $pathPrefix = $this->getDisk()->getAdapter()->getPathPrefix();

        return $pathPrefix.$this->getPathRelativeToRoot();
    }

    /**
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        $base = Str::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(url($base.$path), '/');
    }
}

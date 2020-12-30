<?php

namespace App\Models;

use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithHashids;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel implements Viewable
{
    use HasRandomSeed;
    use HasViews;
    use InteractsWithHashids;
    use InteractsWithViews;
    use QueryCacheable;
    use Searchable;

    /**
     * Invalidate the cache automatically upon update.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    /**
     * Delete all views of an viewable Eloquent model on delete.
     *
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @var int
     */
    public $cacheFor = 3600;

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only([
            'id',
            'name',
        ]);
    }

    /**
     * @return mixed
     */
    public function collections()
    {
        return $this
            ->morphedByMany(Collection::class, 'taggable', 'taggables');
    }

    /**
     * @return mixed
     */
    public function videos()
    {
        return $this
            ->morphedByMany(Video::class, 'taggable', 'taggables');
    }

    /**
     * @param string $type
     *
     * @return int
     */
    public function getItemCountAttribute(string $type = null): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->when($type, fn ($query, $type) => $query->where('taggable_type', $type))
            ->count();
    }

    /**
     * @param Builder $query
     * @param array   $tags
     * @param string  $type
     * @param string  $locale
     *
     * @return Builder
     */
    public function scopeWithSlugTranslated(
        Builder $query,
        array $tags = [],
        string $type = null,
        string $locale = null
    ): Builder {
        $locale = $locale ?? app()->getLocale();

        return $query
            ->when($type, fn ($query, $type) => $query->where('type', $type))
            ->where(function ($query) use ($tags, $locale) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains("slug->{$locale}", $tag);
                }
            });
    }
}

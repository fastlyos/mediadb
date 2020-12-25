<?php

namespace App\Models;

use App\Traits\HasAcquaintances;
use App\Traits\HasActivities;
use App\Traits\HasHashids;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithTags;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeSubscribed;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Collection extends Model implements Viewable
{
    use CanBeSubscribed;
    use HasAcquaintances;
    use HasActivities;
    use HasHashids;
    use HasRandomSeed;
    use HasStatuses;
    use HasTags, InteractsWithTags {
        InteractsWithTags::getTagClassName insteadof HasTags;
        InteractsWithTags::tags insteadof HasTags;
    }
    use HasTranslatableSlug;
    use HasTranslations;
    use HasViews;
    use InteractsWithViews;
    use Searchable;
    use QueryCacheable;

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'json',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    /**
     * @var int
     */
    public $cacheFor = 3600;

    /**
     * @var array
     */
    public $translatable = ['name', 'slug', 'overview'];

    /**
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return string
     */
    public function searchableAs()
    {
        return 'collections';
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only([
            'id',
            'name',
            'type',
            'overview',
        ]);
    }

    /**
     * @return mixed
     */
    public function videos()
    {
        return $this
            ->morphedByMany(Video::class, 'collectable');
    }

    /**
     * @param string|array|\ArrayAccess $values
     * @param string|null               $type
     * @param string|null               $locale
     *
     * @return Collection|static
     */
    public static function findOrCreate($values, string $type = null, string $locale = null)
    {
        $collection = collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $type, $locale);
        });

        return is_string($values) ? $collection->first() : $collection;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $locale
     *
     * @return Collection|static
     */
    protected static function findOrCreateFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $collection = static::findFromString($name, $type, $locale);

        if (!$collection) {
            $collection = static::create([
                'name' => [$locale => $name],
                'type' => $type,
            ]);
        }

        return $collection;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $locale
     *
     * @return Collection|null
     */
    public static function findFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()
            ->where("name->{$locale}", $name)
            ->where('type', $type)
            ->first();
    }

    /**
     * @param string $name
     * @param string $locale
     *
     * @return Collection|null
     */
    public static function findFromStringOfAnyType(string $name, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()
            ->where("name->{$locale}", $name)
            ->first();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return mixed
     */
    public function setAttribute($key, $value)
    {
        if ('name' === $key && !is_array($value)) {
            return $this->setTranslation($key, app()->getLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @return int
     */
    public function getItemCountAttribute(): int
    {
        return $this->videos()->count();
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        return optional($this->videos()->orderByDesc('created_at')->first(), function ($media) {
            return $media->thumbnail_url;
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }
}

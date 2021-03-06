<?php

namespace App\Events\Media;

use App\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HasBeenAdded
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Model
     */
    public Model $model;

    /**
     * @var Media
     */
    public Media $media;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $model, Media $media)
    {
        $this->model = $model;
        $this->media = $media;
    }
}

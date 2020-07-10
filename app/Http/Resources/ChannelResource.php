<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChannelResource extends JsonResource
{
    /**
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->getRouteKey(),
            'slug' => $this->slug,
            'name' => $this->name,
            'thumbnail' => $this->thumbnail,
            'items' => $this->whenAppended('items'),
            'views' => $this->views,
            'created_at' => $this->created_at,
            'relationships' => [
                'media' => MediaResource::collection($this->whenLoaded('media')),
                'model' => new UserResource($this->whenLoaded('model')),
                'tags' => TagResource::collection($this->whenLoaded('tags')),
            ],
        ];
    }
}

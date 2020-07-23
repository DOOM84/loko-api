<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'images' => $this->images,
            'pictures' => $this->whenLoaded('pictures', function () {
                return PictureResource::collection($this->pictures->where('status', 1));
            }),
            'details' => $this->whenLoaded('details', function () {
                return DetailResource::collection($this->details->where('status', 1));
            })
        ];
    }
}

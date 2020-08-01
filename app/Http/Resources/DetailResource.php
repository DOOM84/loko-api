<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class DetailResource extends JsonResource
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
            'status' => $this->status,
            'name' => $this->name,
            'drawing' => $this->drawing,
            'weight' => $this->weight,
            'image' => $this->image ? Storage::disk('public')
                ->url('uploads/Details/original/'.$this->image) : ''
        ];
    }
}

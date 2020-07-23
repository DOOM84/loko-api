<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class adminDetailResource extends JsonResource
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
            'drawing' => $this->drawing,
            'weight' => $this->weight,
            'status' => $this->status,
            'products' => $this->products,
            'ids' => $this->products->pluck('id'),
        ];
    }
}

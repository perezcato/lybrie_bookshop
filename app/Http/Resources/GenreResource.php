<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GenreResource extends JsonResource
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
            'type' => 'GENRES',
            'attributes' => [
                'name' => $this->name,
                'created_at' => $this->created_at->toJSON(),
                'updated_at' => $this->updated_at->toJSON()
            ]
        ];
    }
}

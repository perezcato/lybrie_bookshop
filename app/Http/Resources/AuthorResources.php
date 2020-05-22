<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResources extends JsonResource
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
            'type' => 'AUTHORS',
            'attributes' => [
                'author_name' => $this->author_name,
                'created_at' => $this->created_at->toJSON(),
                'updated_at' => $this->updated_at->toJSON()
            ]
        ];
    }
}

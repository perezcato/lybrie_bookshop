<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'type' => 'BOOKS',
            'attributes' => [
                'title' => $this->title,
                'year' => $this->year,
                'isbn' => $this->isbn,
            ],
            'relationships' => [
                'authors' => [
                    'links' => [
                        'self' => route('books.relationships.authors', ['id' => $this->id]),
                        'related' => route('books.related.authors', ['id' => $this->id])
                    ],
                    'data' => $this->authors->map(function($author) {
                        return [
                            'id' => $author->id,
                            'type' => 'AUTHORS'
                        ];
                    }),
                ],
                'genres' => [
                    'links' => [
                        'self' => route('books.relationships.genres', ['id' => $this->id]),
                        'related' => route('books.related.genres', ['id' => $this->id])
                    ],
                    'data' => $this->genres->map(function($genre) {
                        return [
                            'id' => $genre->id,
                            'type' => 'GENRES'
                        ];
                    }),
                ]
            ]
        ];
    }
}

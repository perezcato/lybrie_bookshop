<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';
    protected $fillable = ['title', 'year', 'isbn', 'author_id', 'genre_id'];

    public function authors()
    {
        return $this->belongsToMany(Author::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }
}

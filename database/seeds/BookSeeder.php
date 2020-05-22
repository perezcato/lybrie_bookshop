<?php

use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
         factory(\App\Genre::class, 5)->create();
         factory(\App\Author::class, 3)->create();
         factory(\App\Book::class, 10)->create();

        $genres = \App\Genre::all();
        $authors = \App\Author::all();
        $books = \App\Book::all();

        $books->each(function($book) use($genres,$authors) {
            $book->authors()->sync(
                $authors->pluck('id')->random(2)
            );
            $book->genres()->sync(
                $genres->pluck('id')->random(2)
            );
        });
    }
}

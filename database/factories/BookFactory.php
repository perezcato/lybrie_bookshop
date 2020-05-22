<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Book;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Book::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'year' => $faker->year,
        'isbn' => Str::random(24),
    ];
});

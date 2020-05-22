<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/books/report','BookController@report')->name('books.report');

Route::apiResource('books','BookController');

Route::get('/books/books/{id}/relationships/authors','BookController@authorRelationship')
    ->name('books.relationships.authors');
Route::get('/books/books/{id}/related/authors','BookController@authorRelated')
    ->name('books.related.authors');

Route::get('/books/books/{id}/relationships/genres','BookController@genreRelationship')
    ->name('books.relationships.genres');
Route::get('/books/books/{id}/related/genres','BookController@genreRelated')
    ->name('books.related.genres');



Route::apiResource('genres','GenreController');
Route::apiResource('authors', 'AuthorController');

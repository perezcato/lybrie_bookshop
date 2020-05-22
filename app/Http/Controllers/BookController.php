<?php

namespace App\Http\Controllers;

use App\Author;
use App\Book;
use App\Genre;
use App\Http\Resources\BookResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        if($request->input('search')){
            $searchParams = $request->input('value');
            if($request->input('search') == 'title'){
                $books = Book::where('title','LIKE',"%{$searchParams}%")->get();
                return (BookResource::collection($books))->response()->setStatusCode(200);
            }
            if($request->input('search') == 'isbn'){
                $books = Book::where('isbn','=',$searchParams)->get();
                return (BookResource::collection($books))->response()->setStatusCode(200);
            }
        }
        if($request->input('browseby')){
            $browseParam = $request->input('value');
            if($request->input('browseby') == 'author'){
                $books = Author::find($browseParam)->books;
                return (BookResource::collection($books))->response()->setStatusCode(200);
            }
            if($request->input('browseby') == 'genre'){
                $books = Genre::find($browseParam)->books;
                return (BookResource::collection($books))->response()->setStatusCode(200);
            }
            if($request->input('browseby') == 'year'){
                $books = Book::where('year','=',$browseParam)->get();
                return (BookResource::collection($books))->response()->setStatusCode(200);
            }
        }
        return (BookResource::collection(Book::all()))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
           'type' => 'required|in:BOOKS',
            'attributes' => 'required|array',
            'attributes.title' => 'required|string',
            'attributes.isbn' => 'required|string',
            'attributes.year' => 'required|string',
            'attributes.author_id' => 'required|array|exists:authors,id',
            'attributes.genre_id' => 'required|array|exists:authors,id',
        ]);

        $book = Book::create([
            'title' => $request->input('attributes.title'),
            'isbn' => $request->input('attributes.isbn'),
            'year' => $request->input('attributes.year')
        ]);
        $book->authors()->sync($request->input('attributes.author_id'));
        $book->genres()->sync($request->input('attributes.genre_id'));
        return (new BookResource($book))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json([
                'message' => 'User does not exists.',
                'errors' => [
                    'user' => 'User does not exist'
                ]
            ], 404);}
        return (new BookResource(Book::find($id)))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
           'type' => 'required|in:BOOKS',
           'attributes' => 'required|array',
            'attrubutes.title' => 'string',
            'attributes.isbn' => 'string',
            'attributes.year' => 'string',
            'attributes.author_id' => 'numeric|exists:authors,id',
            'attributes.genre_id' => 'numeric|exists:authors,id',
        ]);
        $book = Book::find($id);
        if(!$book){
            return response()->json([
            'message' => 'User does not exists.',
            'errors' => [
                'user' => 'User does not exist'
            ]
        ], 404);}
        $book->update($request->input('attributes'));
        return (new BookResource($book))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        $book = Book::find($id);
        if(!$book){
            return response()->json([
                'message' => 'User does not exists.',
                'errors' => [
                    'user' => 'User does not exist'
                ]
            ], 404);}
        $book->delete();
        return response()->json([],204);
    }

    public function report(Request $request)
    {
        $sortParam = $request->input('by');
        $paramId = $request->input('id');
        if(strtolower($sortParam)=='author'){
            if(!$paramId){
                return response()->json(['data' => 'invalid id']);
            }
            $authorBooks = Author::find($paramId)->books->count();
            return response()->json(['data' => [
                'number' => $authorBooks
            ]]);
        }
        if(strtolower($sortParam)=='genre'){
            if(!$paramId){
                return response()->json(['data' => 'invalid id']);
            }
            $authorBooks = Genre::find($paramId)->books->count();
            return response()->json(['data' => [
                'number' => $authorBooks
            ]]);
        }
        if(strtolower($sortParam)=='year'){
            if(!$paramId){
                return response()->json(['data' => 'invalid id']);
            }
            $authorBooks = Book::where('year','=',$paramId)->count();
            return response()->json(['data' => [
                'number' => $authorBooks
            ]]);
        }
        if(strtolower($sortParam)!='author' || strtolower($sortParam)!='genre'|| strtolower($sortParam)!='year'){
            return response()->json(['data' => 'invalid sort param']);
        }


    }
}

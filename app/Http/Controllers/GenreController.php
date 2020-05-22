<?php

namespace App\Http\Controllers;

use App\Genre;
use App\Http\Resources\GenreResource;
use Illuminate\Http\Request;
use PHPUnit\Framework\StaticAnalysis\HappyPath\AssertNotInstanceOf\A;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return (GenreResource::collection(Genre::all()))->response()->setStatusCode(200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required|in:GENRES',
            'attributes' => 'required|array',
            'attributes.name' => 'required|string'
        ]);
        $book = Genre::create($request->input('attributes'));
        return (new GenreResource($book))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $genre = Genre::find($id);
        if(!$genre){
            return response()->json([
                'message' => 'genre does not exists.',
                'errors' => [
                    'genre' => 'genre does not exist'
                ]
            ], 404);}
        return (new GenreResource(Genre::find($id)))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'type' => 'required|in:GENRES',
            'attributes' => 'required|array',
            'attributes.name' => 'string'
        ]);
        $genre = Genre::find($id);
        if(!$genre){
            return response()->json([
                'message' => 'genre does not exists.',
                'errors' => [
                    'genre' => 'genre does not exist'
                ]
            ], 404);}
        $genre->update($request->input('attributes'));
        return (new GenreResource($genre))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $genre = Genre::find($id);
        if(!$genre){
            return response()->json([
                'message' => 'genre does not exists.',
                'errors' => [
                    'genre' => 'genre does not exist'
                ]
            ], 404);}
        $genre->delete();
        return response()->json([],204);
    }
}

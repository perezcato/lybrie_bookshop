<?php

namespace App\Http\Controllers;

use App\Author;
use App\Http\Resources\AuthorResources;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return (AuthorResources::collection(Author::all()))->response()->setStatusCode(200);
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
            'type' => 'required|in:AUTHORS',
            'attributes' => 'required|array',
            'attributes.author_name' => 'required|string'
        ]);
        $author = Author::create($request->input('attributes'));
        return (new AuthorResources($author))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $author = Author::find($id);
        if(!$author){
            return response()->json([
                'message' => 'author does not exists.',
                'errors' => [
                    'author' => 'author does not exist'
                ]
            ], 404);}
        return (new AuthorResources(Author::find($id)))->response()->setStatusCode(200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'type' => 'required|in:AUTHORS',
            'attributes' => 'required|array',
            'attributes.author_name' => 'string'
        ]);
        $authors = Author::find($id);
        if(!$authors){
            return response()->json([
                'message' => 'author does not exists.',
                'errors' => [
                    'author' => 'author does not exist'
                ]
            ], 404);}
        $authors->update($request->input('attributes'));
        return (new AuthorResources($authors))->response()->setStatusCode(200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $author = Author::find($id);
        if(!$author){
            return response()->json([
                'message' => 'author does not exists.',
                'errors' => [
                    'author' => 'author does not exist'
                ]
            ], 404);}
        $author->delete();
        return response()->json([],204);
    }
}

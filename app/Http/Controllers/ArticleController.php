<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

use App\Http\Requests\ArticleCreateRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Http\Requests\PageRequest;

class ArticleController extends ApiController
{

    public function __construct()
    {
        $this->middleware('auth:api')->only('store');
        $this->middleware('admin')->only('publish');
        $this->middleware('owner')->only('update', 'destroy');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(PageRequest $pageRequest)
    {
        return $this->indexHelper(Article::where('is_published', 1), $pageRequest);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\ArticleCreateRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleCreateRequest $request)
    {
        $article = new Article;
        $article->title = $request->get('title');
        $article->description = $request->get('description');
        $article->author_id = request()->user()->id;

        if(!$article->save()){
            return response()->json([
                'message' => 'Could not create article',
            ], 422);
        }

        return response()->json([
            'message' => 'Article created',
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        if($article->is_published){
            return $article;
        }
        return response()->json([
            'message' => 'Resource not found',
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ArticleUpdateRequest  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(ArticleUpdateRequest $request, Article $article)
    {
        $article->update($request->except(['is_published', 'author_id']));
        return response()->json([
            'message' => 'Article updated',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        $article->delete();
        return response()->json([
            'message' => 'Article deleted',
        ], 200);
    }

    /**
     * Publish the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function publish(Article $article)
    {
        $article->update(['is_published', 1]);
        return response()->json([
            'message' => 'Article published',
        ], 200);
    }
}

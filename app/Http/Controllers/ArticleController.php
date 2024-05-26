<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Events\ArticleEvent;
use App\Policies\Responce;

class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currentPage = request('page') ? request('page') : 1;
        $articles = Cache::remember('articles'.$currentPage, 3000, function(){
            return Article::latest()->paginate(5);        
        });
        if(request()->expectsJson()) return response()->json($articles);
        return view('article.index', ['articles'=>$articles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('create', [self::class]);
        return view('article.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $keys = DB::table('cache')
                ->select('key')
                ->whereRaw('`key` GLOB :key', [':key' => 'articles*[0-9]'])->get();
        foreach($keys as $key){
            Cache::forget($key->key);
        }

        $request->validate([
            'date'=>'required',
            'name'=>'required|min:6',
            'desc'=>'required'
        ]);
        $article = new Article;
        $article->date = $request->date;
        $article->name = request('name');
        $article->desc = request('desc');
        $article->user_id = 1;
        $res = $article->save();
        if($res) ArticleEvent::dispatch($article);
        if(request()->expectsJson()) return response()->json($res);
        return redirect()->route('article.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function show(Article $article)
    {
        $comments = Cache::rememberForever('article_comment'.$article->id, function()use($article){
            return Comment::where('article_id', $article->id)->where('accept', true)->latest()->get();
        });
        return view('article.show', ['article'=>$article], ['comments'=>$comments]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function edit(Article $article)
    {
        $this->authorize('update', $article);
        return view('article.edit', ['article'=>$article]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Article $article)
    {
        $keys = DB::table('cache')
        ->select('key')
        ->whereRaw('`key` GLOB :key', [':key' => 'articles*[0-9]'])->get();
        foreach($keys as $key){
            Cache::forget($key->key);
        }

        $request->validate([
            'date'=>'required',
            'name'=>'required|min:6',
            'desc'=>'required'
        ]);
        $article->date = $request->date;
        $article->name = request('name');
        $article->desc = request('desc');
        $article->user_id = 1;
        $res = $article->save();
        if(request()->expectsJson()) return response()->json($res);
        return redirect()->route('article.show', ['article'=>$article->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Article  $article
     * @return \Illuminate\Http\Response
     */
    public function destroy(Article $article)
    {
        Cache::flush();
        Gate::authorize('delete', [self::class, $article]);
        $res = $article->delete();
        if(request()->expectsJson()) return response()->json($res);
        return redirect()->route('article.index');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Mail\MailNewComment;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\Article;
use Illuminate\Support\Facades\DB;
use App\Jobs\VeryLongJob;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $comments = Cache::rememberForever('comments', function(){
            return
            DB::table('comments')
                    ->join('users', 'users.id', '=', 'comments.user_id')
                    ->join('articles', 'articles.id', '=', 'comments.article_id')
                    ->select('comments.*', 'users.name', 'articles.name as article_name')
                    ->get();
        });
        //Log::alert($comments);
        if(request()->expectsJson()) return response()->json($comments);
        return view('comment.index', ['comments'=>$comments]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function accept(Comment $comment){
        Cache::forget('comments');
        Cache::forget('article_comment'.$comment->article_id);
        $comment->accept = 'true';
        $comment->save();
        if(request()->expectsJson()){return response()->json("comment $comment->id accept");}
        return redirect()->route('comment.index');
    }

    public function reject(Comment $comment){
        Cache::forget('comments');
        Cache::forget('article_comment'.$comment->article_id);
        $comment->accept = 'false';
        $comment->save();
        if(request()->expectsJson()){return response()->json("comment $comment->id reject");}
        return redirect()->route('comment.index');
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Cache::forget('comments');
        $request->validate([
            'title'=>'required',
            'text'=>'required'
        ]);

        $article = Article::findOrFail($request->article_id);

        $comment = new Comment;
        $comment->title = $request->title;
        $comment->text = request('text');
        $comment->article_id = request('article_id');
        $comment->user_id = 1;
        $res = $comment->save();

        if ($res) {
            VeryLongJob::dispatch($comment, $article);
        }
        if(request()->expectsJson()) return response()->json($res);
        return redirect()->route('article.show', ['article'=>request('article_id')])->with(['res'=>$res]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function edit(Comment $comment)
    {
        return view('comment.edit', ['comment'=>$comment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        Cache::flush();
        $request->validate([
            'title'=>'required',
            'text'=>'required'
        ]);
        $comment->title = request('title');
        $comment->text = request('text');
        $comment->save();
        return redirect()->route('article.show', ['article'=>$comment->article_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        Cache::flush();
        $comment->delete();
        return redirect()->route('article.show', ['article'=>$comment->article_id]);
    }
}

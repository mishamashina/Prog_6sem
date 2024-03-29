<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Article::class => ArticleControllerPolicy::class,
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        
        Gate::before(function(User $user){
            if ($user->role == 'moderator') return true;
        });


        Gate::define('comment', function(User $user, Comment $comment){
            if ($user->id === $comment->user_id){
            return Response::allow();}
            return Response::deny('В доступе отказано!');
        });
        //
    }
}

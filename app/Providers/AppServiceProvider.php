<?php

namespace App\Providers;

use App\Models\Comments;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Checks weather the authenticated user has the permission to delete a comment
        Gate::define('delete-comment', function (User $user, Comments $comment) {
            return $comment->user_id === $user->id || $user->isAdmin();
        });
    }
}

<?php

namespace App\Repositories;

use App\Models\Posts;
use App\Models\User;
use App\Services\DateService;
use Illuminate\Support\Facades\Auth;

interface PostRepositoryInterface
{
    public function getPostsBasedOnUser();
}

class PostRepository implements PostRepositoryInterface
{
    protected $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    /**
     * Retrieves the posts owned and published by the authenticated user, or all published posts if the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of posts.
     */
    public function getPostsBasedOnUser()
    {
        if (!Auth::check()) {
            return Posts::publishedWithDetails();
        }

        $authUserId = Auth::id();

        if (User::isAdmin()) {
            return Posts::allWithDetails();
        }

        return Posts::allWithDetails()->where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)->orWhere('status', 1);
        });
    }

}

<?php

namespace App\Repositories;

use App\Models\Posts;
use App\Services\DateService;

interface PostRepositoryInterface
{
    public function getUsersOwnedAndPublishedPosts($userId);
}

class PostRepository implements PostRepositoryInterface
{
    protected $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    public function getUsersOwnedAndPublishedPosts($userId)
    {
        // get posts function user conditions to be here

        return Posts::allWithDetails()->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('status', 1);
                });
    }
}

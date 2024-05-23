<?php

namespace App\Repositories;

use App\Models\Comments;
use Illuminate\Support\Collection;

interface CommentsRepositoryInterface
{
    public function getCommentsForAPost(int $postId): Collection;
}

class CommentsRepository implements CommentsRepositoryInterface
{
    public function getCommentsForAPost(int $postId): Collection
    {
        return Comments::latest()
            ->where('posts_id', $postId)
            ->with('user')
            ->select('id', 'posts_id', 'user_id', 'body', 'created_at')
            ->get();
    }
}

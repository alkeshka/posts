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
    /**
     * Retrieves the latest comments for a given post ID, along with the associated user information.
     *
     * @param int $postId The ID of the post.
     * @return \Illuminate\Support\Collection The collection of comments for the post, with each comment containing the ID, post ID, user ID, body, and creation date.
     */
    public function getCommentsForAPost(int $postId): Collection
    {
        return Comments::latest()
            ->where('posts_id', $postId)
            ->with('user')
            ->select('id', 'posts_id', 'user_id', 'body', 'created_at')
            ->get();
    }
}

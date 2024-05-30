<?php

namespace App\Repositories;

use App\Models\Comments;
use Illuminate\Support\Collection;

interface CommentsRepositoryInterface
{
    public function fetchCommentsForPost(int $postId): Collection;
}

class CommentsRepository implements CommentsRepositoryInterface
{

    protected $commentModel;


    public function __construct(
        Comments $comment,
    ) {
        $this->commentModel = $comment;
    }

    /**
     * Retrieves the latest comments for a given post ID, along with the associated user information.
     *
     * @param int $postId The ID of the post.
     * @return \Illuminate\Support\Collection The collection of comments for the post, with each comment containing the ID, post ID, user ID, body, and creation date.
     */
    public function fetchCommentsForPost(int $postId): Collection
    {
        return $this->commentModel->latest()
            ->where('posts_id', $postId)
            ->with('user')
            ->select('id', 'posts_id', 'user_id', 'body', 'created_at')
            ->get();
    }

    /**
     * Create a new comment with the given attributes.
     *
     * @param array $attributes The attributes of the comment to be created.
     * @return \App\Models\Comments The newly created comment.
     */
    public function create(array $attributes)
    {
        return $this->commentModel->create($attributes);
    }
}

<?php

namespace App\Services;

use App\Models\Comments;
use App\Repositories\CommentsRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CommentService
{
    protected $commentsRepository;
    /**
     * Constructs a new instance of the class.
     *
     * @param CommentsRepository $commentsRepository The repository for managing comments.
     */
    public function __construct(
        CommentsRepository $commentsRepository,
    ) {
        $this->commentsRepository = $commentsRepository;
    }

    /**
     * Deletes a comment if the user is the owner of the comment or an admin.
     *
     * @param Comments $comment The comment to be deleted.
     * @return array An array with a message and type indicating the success or failure of the deletion.
     */
    public function deleteComment(Comments $comment)
    {

        if (Gate::allows('delete-comment', $comment)) {
            $comment->delete();
            return [
                'message' => 'Comment deleted successfully',
                'type' => 'success'
            ];
        }

        return [
            'message' => 'You do not have permission to delete this comment',
            'type' => 'failure'
        ];
    }

    /**
     * Create a new comment for the authenticated user.
     *
     * @param array $validatedAttributes The validated attributes for the comment.
     * @return array The success message and type.
     */
    public function createComment(array $validatedAttributes)
    {
        $validatedAttributes['user_id'] = Auth::id();
        $this->commentsRepository->create($validatedAttributes);

        return [
            'message' => 'Your comment has been posted. Thank you!',
            'type' => 'success'
        ];
    }

    /**
     * Retrieves the comments for a given post ID.
     *
     * @param int $postId The ID of the post.
     * @return Collection The collection of comments for the post.
     */
    public function getCommentsForPost($postId)
    {
        return $this->commentsRepository->fetchCommentsForPost($postId);
    }
}

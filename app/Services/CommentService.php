<?php

namespace App\Services;

use App\Models\Comments;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommentService
{
    /**
     * Deletes a comment if the user is the owner of the comment or an admin.
     *
     * @param Comments $comment The comment to be deleted.
     * @return array An array with a message and type indicating the success or failure of the deletion.
     */
    public function deleteComment(Comments $comment)
    {
        if ($comment->user_id === Auth::id() || User::IsAdmin()) {
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
        Comments::create($validatedAttributes);

        return [
            'message' => 'Your comment has been posted. Thank you!',
            'type' => 'success'
        ];
    }
}

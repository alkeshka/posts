<?php

namespace App\Services;

use App\Models\Comments;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommentService
{
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

    public function createComment(array $validatedAttributes)
    {
        Auth::user()->comments()->create($validatedAttributes);

        return [
            'message' => 'Your comment has been posted. Thank you!',
            'type' => 'success'
        ];
    }
}

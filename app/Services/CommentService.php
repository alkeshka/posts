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
            return $comment->delete();
        }

        return false;
    }
}

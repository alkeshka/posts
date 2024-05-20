<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function store(Request $request)
    {
        $validatedAttributes = $request->validate([
            'posts_id' => ['required', 'exists:posts,id'],
            'body' => ['required', 'min:3', 'max:225'],
        ]);

        $comment = Auth::user()->comments()->create($validatedAttributes);

        return redirect(route('posts.show', $comment->posts_id));
    }

    public function destroy(Comments $comment)
    {
        if(Auth::user()->id === $comment->user_id) {
            $status = $comment->delete();
        }
        
        return redirect()->back();
    }
}

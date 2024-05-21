<?php

namespace App\Http\Controllers;

use App\Models\Comments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    public function index(String $id)
    {
        return Comments::latest()
            ->where('posts_id', $id)
            ->with('user')
            ->select('id', 'posts_id', 'user_id', 'body', 'created_at')
            ->get();
    }

    public function store(Request $request)
    {
        $validatedAttributes = $request->validate([
            'posts_id' => ['required', 'exists:posts,id'],
            'body' => ['required', 'min:3', 'max:225'],
        ]);

        Auth::user()->comments()->create($validatedAttributes);

        return redirect()->back();
    }

    public function destroy(Comments $comment)
    {
        return $comment->user_id === Auth::id()
            ? $comment->delete() && redirect()->back()
            : redirect()->back();
    }
}

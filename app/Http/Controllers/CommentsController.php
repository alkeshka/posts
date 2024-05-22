<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comments;
use App\Repositories\CommentsRepository;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{

    protected $commentsRepository;

    public function __construct(CommentsRepository $commentsRepository)
    {
        $this->commentsRepository = $commentsRepository;
    }


    public function index(String $postId)
    {
        $postAssociatedComments = $this->commentsRepository->getLatestForPost($postId);
        return $postAssociatedComments;
    }

    public function store(StoreCommentRequest $request)
    {
        $validatedAttributes = $request->validated();

        Auth::user()->comments()->create($validatedAttributes);

        return redirect()->back();
    }

    public function destroy(Comments $comment)
    {
        return $comment->user_id === Auth::id() || Auth::user()->user_role_id == 1
            ? $comment->delete() && redirect()->back()
            : redirect()->back();
    }
}

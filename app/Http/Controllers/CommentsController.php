<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comments;
use App\Repositories\CommentsRepository;
use App\Services\CommentService;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{

    protected $commentsRepository;
    protected $commentService;

    public function __construct(
        CommentsRepository $commentsRepository,
        CommentService $commentService
    ) {
        $this->commentsRepository = $commentsRepository;
        $this->commentService = $commentService;
    }


    public function index(String $postId)
    {
        return $this->commentsRepository->getCommentsForAPost($postId);
    }

    public function store(StoreCommentRequest $request)
    {
        $validatedAttributes = $request->validated();
        $status = $this->commentService->createComment($validatedAttributes);

        return redirect()->back()->with('status', $status);
    }

    public function destroy(Comments $comment)
    {
        $status = $this->commentService->deleteComment($comment);
        return redirect()->back()->with('status', $status);
    }
}

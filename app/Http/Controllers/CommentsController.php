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
        $postAssociatedComments = $this->commentsRepository->getCommentsForAPost($postId);
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
        if ($this->commentService->deleteComment($comment)) {
            $status = [
                'message' => 'Comment deleted successfully',
                'class' => 'text-green-500 m-2'
            ];
        } else {
            $status = [
                'message' => 'You do not have permission to delete this comment',
                'class' => 'text-red-500 m-2'
            ];
        }

        return redirect()->back()->with('status', $status);
    }
}

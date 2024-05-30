<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comments;
use App\Repositories\CommentsRepository;
use App\Services\CommentService;

class CommentsController extends Controller
{
    protected $commentsRepository;
    protected $commentService;

    /**
     * Constructs a new instance of the class.
     *
     * @param CommentsRepository $commentsRepository The repository for managing comments.
     * @param CommentService $commentService The service for managing comments.
     */
    public function __construct(
        CommentsRepository $commentsRepository,
        CommentService $commentService
    ) {
        $this->commentsRepository = $commentsRepository;
        $this->commentService = $commentService;
    }

    /**
     * Retrieves the comments for a given post ID.
     *
     * @param string $postId The ID of the post.
     * @return Collection The collection of comments for the post.
     */
    public function index(String $postId)
    {
        return $this->commentsRepository->getCommentsForAPost($postId);
    }

    /**
     * Store a new comment.
     *
     * @param StoreCommentRequest $request The request object containing the validated comment attributes.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a status message.
     */
    public function store(StoreCommentRequest $request)
    {
        $validatedAttributes = $request->validated();
        $status = $this->commentService->createComment($validatedAttributes);

        return redirect()->back()->with('status', $status);
    }

    /**
     * Store a new comment.
     *
     * @param StoreCommentRequest $request The request object containing the validated comment attributes.
     * @return \Illuminate\Http\RedirectResponse Redirects back with a status message.
     */
    public function destroy(Comments $comment)
    {
        $status = $this->commentService->deleteComment($comment);
        return redirect()->back()->with('status', $status);
    }
}

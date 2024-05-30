<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comments;
use App\Services\CommentService;

class CommentsController extends Controller
{
    protected $commentService;

    /**
     * Constructs a new instance of the class.
     *
     * @param CommentService $commentService The service for managing comments.
     */
    public function __construct(
        CommentService $commentService
    ) {
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
        return $this->commentService->getCommentsForPost($postId);
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

<?php

namespace App\Http\Controllers;

use App\Repositories\PostRepository;
use App\Services\FilterService;
use App\Services\PostService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FilterController extends Controller
{

    protected $postService;
    protected $postRepository;
    protected $filterService;

    public function __construct(
        PostRepository $postRepository,
        FilterService $filterService,
        PostService $postService,
    ) {
        $this->postRepository = $postRepository;
        $this->filterService = $filterService;
        $this->postService = $postService;
    }

    public function __invoke(Request $request)
    {

        $postQuery = $this->postService->getPostsBasedOnUser();

        $postQuery = $this->filterService->applyAuthorFilter($postQuery, $request->author);
        $postQuery = $this->filterService->applyTagFilter($postQuery, $request->category);
        $postQuery = $this->filterService->applyCommentCountFilter($postQuery, $request->noOfComments);
        $postQuery = $this->filterService->applyPublishedDateFilter($postQuery, $request->publishedDate);
        $postQuery = $this->filterService->applySearchQueryFilter($postQuery, $request->searchQuery);

        return $postQuery->with(['tags', 'user'])->get();
    }
}

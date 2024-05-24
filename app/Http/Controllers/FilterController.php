<?php

namespace App\Http\Controllers;

use App\Services\FilterService;
use App\Services\PostService;
use Illuminate\Http\Request;

class FilterController extends Controller
{

    protected $postService;
    protected $filterService;

    public function __construct(
        FilterService $filterService,
        PostService $postService,
    ) {
        $this->filterService = $filterService;
        $this->postService = $postService;
    }

    public function __invoke(Request $request)
    {
        $postQuery = $this->postService->getPostsBasedOnUser();
        $postQuery = $this->filterService->applyFilters($postQuery, $request);

        return $postQuery->with(['tags', 'user'])->get();
    }
}

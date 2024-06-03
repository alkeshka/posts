<?php

namespace App\Http\Controllers;

use App\Models\Tags;
use App\Services\TagService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    protected $tagService;

    /**
     * Constructs a new instance of the class.
     *
     * @param TagService $tagService The service for managing tags.
     */
    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * Retrieves tags based on the search term from the request and returns them as a JSON response.
     *
     * @param Request $request The HTTP request object containing the search term.
     * @return JsonResponse The JSON response containing the tags.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $tags = $this->tagService->getTags($search);

        return response()->json($tags);
    }

    /**
     * Retrieves the posts associated with a given tag.
     *
     * @param Tags $tag The tag object.
     * @return View The view displaying the posts associated with the tag.
     */
    public function getAssociatedPosts(Tags $tags): View
    {
        $associatedPosts = $tags->posts()->withCount('comments')->simplePaginate();
        return view('tags.posts', compact('tags', 'associatedPosts'));
    }
}

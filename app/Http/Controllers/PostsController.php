<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Posts;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Services\FilterService;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    protected $postService;
    protected $postRepository;
    protected $filterService;

    /**
     * Constructs a new instance of the class.
     *
     * @param PostService $postService The service for handling posts.
     * @param PostRepository $postRepository The repository for handling posts.
     * @param FilterService $filterService The service for filtering posts.
     */
    public function __construct(
        PostService $postService,
        PostRepository $postRepository,
        FilterService $filterService
   ) {
        $this->postService = $postService;
        $this->postRepository = $postRepository;
        $this->filterService = $filterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $postAuthors = $this->postService->getPostAuthors();
        $tags = $this->postService->getTags();

        return view('posts.index', [
            'postAuthors' => $postAuthors,
            'tags' => $tags,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        //get data for post creation
        $validatedAttributes = $request->validated();

        $thumbnailPath = $request->thumbnail->store('thumbnail', 'public');

        $validatedAttributes['thumbnail'] = $thumbnailPath;
        $validatedAttributes['user_id']   = Auth::id();

        $post = Posts::create(Arr::except($validatedAttributes, 'categories'));

        if ($validatedAttributes['categories'] ?? false) {
            $this->postService->tagsSync($validatedAttributes['categories'], $post);
        }

        $status = [
                'message' => 'Post created successfully!',
                'type' => 'success'
            ];

        return redirect('/')->with('status', $status);
    }

    /**
     * Display the specified resource.
     */
    public function show(Posts $post)
    {
        return view('posts.show', [
            'post' => $post->load('comments.user'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Posts $post)
    {
        return view('posts.edit', [ 'post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Posts $post)
    {
        $validatedAttributes = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $validatedAttributes['thumbnail'] = $this->postService->replaceThumbnail($request->thumbnail, $post);
        }

        $post->update(Arr::except($validatedAttributes, 'categories'));

        $this->postService->tagsSync($validatedAttributes['categories'], $post);

        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Posts $post): RedirectResponse
    {
        if (User::isAdmin()) {
            $this->postService->deletePost($post);
        }
        return redirect('/');
    }

    /**
     * Retrieves the data for displaying posts based on the given request by the Ajax Data Table.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse The JSON response containing the posts data.
     */
    public function getPostsData(Request $request)
    {
        $postLists          = $this->postRepository->getPostsBasedOnUser();
        $totalPostsCount    = $postLists->count();
        $postLists          = $this->postService->applySorting($request, $postLists);
        $postLists          = $this->filterService->applyFilters($postLists, $request);
        $paginatedPosts     = $this->postService->getPaginate($request, $postLists);
        $formattedData      = $this->postService->getFormatData($paginatedPosts);
        $filteredPostsCount = $postLists->count();
        $jsonData           = $this->postService->createJsonResponse($request, $totalPostsCount, $filteredPostsCount, $formattedData);

        return response()->json($jsonData);
    }


}

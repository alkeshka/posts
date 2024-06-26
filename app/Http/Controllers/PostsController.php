<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Posts;
use App\Models\User;
use App\Services\FilterService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PostsController extends Controller
{
    protected $postService;
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
        FilterService $filterService
    ) {
        $this->postService = $postService;
        $this->filterService = $filterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('posts.index');
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
        $this->postService->createPost($request->validated());

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
        $this->postService->updatePost($request->validated(), $post);

        return redirect('/')->with('status', [
            'message' => 'Post updated successfully!',
            'type' => 'success'
        ]);
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
        $jsonData = $this->postService->getPostsData($request);

        return response()->json($jsonData);
    }

    /**
     * Retrieves the authors of posts based on the search term from the request and returns them as a JSON response.
     *
     * @param Request $request The HTTP request object containing the search term.
     * @return JsonResponse The JSON response containing the authors of posts.
     */
    public function getPostAuthors(Request $request): JsonResponse
    {
        $searchTerm = $request->input('search', '');
        $authors = $this->postService->getPostAuthors($searchTerm);

        return response()->json($authors);
    }
}

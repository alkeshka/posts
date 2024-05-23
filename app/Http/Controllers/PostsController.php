<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;

class PostsController extends Controller
{
    /**
     * Constructs a new instance of the class.
     *
     * @param PostService $postService The PostService instance to be used.
     */
    protected $postService;
    protected $postRepository;

    public function __construct(
        PostService $postService,
        PostRepository $postRepository
   ) {
        $this->postService = $postService;
        $this->postRepository = $postRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if(!Auth::check()) {
            $postsWithDetails = Posts::publishedWithDetails()->paginate(4);
        } else {
            $authUser = Auth::user();
            if ($authUser->users_role_id == 1) {
                $postsWithDetails = Posts::allWithDetails()->paginate(4);
            } else {
                $postsWithDetails = $this->postRepository->getUsersOwnedAndPublishedPosts($authUser->id)->paginate(4);
            }

        }

        $postAuthors    = User::postAuthors()->get();
        $tags           = Tags::all();
        $publishedDates = $this->postRepository->getFormattedPublishedDates();
        $commentsCounts = $this->postRepository->getPostsCommentsCounts()->toArray();
        $tags           = Tags::all();

        return view('posts.index', [
            'postAuthors' => $postAuthors,
            'tags' => $tags,
            'publishedDates' => $publishedDates,
            'posts' => $postsWithDetails,
            'commentsCounts' => $commentsCounts
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
        $validatedAttributes = $request->validated();

        $thumbnailPath = $request->thumbnail->store('thumbnail', 'public');

        $validatedAttributes['thumbnail'] = $thumbnailPath;

        $post = Auth::user()->posts()->create(Arr::except($validatedAttributes, 'categories'));

        if ($validatedAttributes['categories'] ?? false) {
            $this->postService->attachTags($post, $validatedAttributes['categories']);
        }

        return redirect('/');
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
    public function destroy(Posts $post)
    {
        if (Auth::user()->users_role_id == 1) {
            $post->delete();
        }

        return redirect('/');
    }

}

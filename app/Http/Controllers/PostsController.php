<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Posts;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Services\PostService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

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
        // $postsWithDetails = $this->postService->getPostsBasedOnUser()->paginate(2);

        $postAuthors = $this->postService->getPostAuthors();
        $tags = $this->postService->getTags();

        // remove it from here
        $publishedDates = $this->postService->getPublishedDates();

        return view('posts.index', [
            'postAuthors' => $postAuthors,
            'tags' => $tags,
            'publishedDates' => $publishedDates,
            // 'posts' => $postsWithDetails,
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
        $validatedAttributes['user_id']   = Auth::id();

        $post = Posts::create(Arr::except($validatedAttributes, 'categories'));

        if ($validatedAttributes['categories'] ?? false) {
            $this->postService->attachTags($post, $validatedAttributes['categories']);
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
    public function destroy(Posts $post)
    {
        // delete the connected tags also
        if (User::isAdmin()) {
            $post->delete();
        }

        return redirect('/');
    }

    public function getPostsData(Request $request)
    {
        $columns = ['id', 'title', 'author', 'comments_count', 'tags', 'created_at', 'actions'];

        $totalData = Posts::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $orderColumnIndex = $request->input('order.0.column');
        $order = $columns[$orderColumnIndex];
        $dir = $request->input('order.0.dir');

        $query = Posts::with('user', 'tags')
            ->select('posts.*')->withCount('comments');

        // Apply minimum comments filter
        if ($request->has('noOfComments') && $request->input('noOfComments') != '') {
            $noOfComments = $request->input('noOfComments');
            $query->where('comments_count', '=', (int) $noOfComments);
        }

        if ($request->has('searchQuery') && $request->input('searchQuery') != '') {
            $search = $request->input('searchQuery');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }


        if ($request->has('author') && $request->input('author') != '') {
            $query->where('user_id', $request->author);
        }

        if ($request->has('category') && $request->input('category') != '') {
            $tagId = $request->category;
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        $posts = $query->offset($start)
            ->limit($limit)
            ->orderBy($order, $dir)
            ->get();

        $totalFiltered = $query->count();

        $data = array();
        if (!empty($posts)) {
            foreach ($posts as $index => $post) {
                $nestedData['id'] = $index + 1;
                $nestedData['title'] = $post->title;
                $nestedData['author'] = $post->user->first_name . ' ' . $post->user->last_name;
                $nestedData['comments_count'] = '<button onclick="loadComments(' . $post->id . ')">' . $post->comments_count . '</button>';
                $nestedData['tags'] = $post->tags->pluck('name')->implode(', ');
                $nestedData['created_at'] = $post->created_at;
                $nestedData['actions'] = '
                <a href="/posts/' . $post->id . '" class="font-medium text-blue-600 text-blue-500 hover:underline"><i class="fa fa-eye" style="font-size:18px"></i></a>
                ' . (auth()->check() && (auth()->id() == $post->user_id || User::isAdmin()) ? '<a href="/posts/' . $post->id . '/edit" class="font-medium text-blue-600 text-blue-500 hover:underline"><i class="fa fa-edit" style="font-size:18px"></i></a>' : '') . '
                ' . (auth()->check() && User::isAdmin() ? '<a onclick="return confirm(\'Are you sure?\')" href="/posts/' . $post->id . '/delete" class="font-medium text-blue-600 text-blue-500 hover:underline"><i class="fa fa-trash-o text-red-500" style="font-size:18px"></i></a>' : '');

                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data
        );

        return response()->json($json_data);
    }


}

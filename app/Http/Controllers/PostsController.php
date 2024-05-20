<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Arr;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Posts::latest()->where('status',1)->with(['tags','user'])->withCount('comments')->get();
        return view('posts.index', [ 'posts' => $posts]);
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
    public function store(Request $request)
    {
        
        $validatedAttributes = $request->validate([
            'title' => ['required', 'min:3', 'max:20'],
            'body' => ['required', 'min:3', 'max:225'],
            'status' => ['required'],
            'thumbnail' => ['required', File::types(['png', 'jpg', 'jpeg'])->max(10240)],
            'categories' => ['required'],
        ]);

       
        $thumbnailPath = $request->thumbnail->store('thumbnail');
        $validatedAttributes['thumbnail'] = $thumbnailPath;

        $post = Auth::user()->posts()->create(Arr::except($validatedAttributes, 'categories'));

        if ($validatedAttributes['categories'] ?? false) {
            foreach (explode(',', $validatedAttributes['categories']) as $tag) {
                $post->tag($tag);
            }
        }

        return redirect('/');
    }

    /**
     * Display the specified resource.
     */
    public function show(Posts $post)
    {
        $post->load('comments.user');  // Eager load comments and user

        if (!$post) {
            abort(404); 
        }

        return view('posts.show', ['post' => $post]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

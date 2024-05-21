<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Models\Tags;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $publishedPosts = Posts::publishedWithDetails()->simplePaginate(4);
        $postAuthors    = User::postAuthors()->get();
        $tags           = Tags::all();
        $publishedDates = Posts::formattedPublishedDates()->toArray();
        $commentsCounts = Posts::PostsCommentsCounts()->toArray();
        $tags           = Tags::all();
 

        return view('posts.index', [
            'postAuthors' => $postAuthors,
            'tags' => $tags,
            'publishedDates' => $publishedDates,
            'posts' => $publishedPosts,
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
    public function store(Request $request)
    {

        $validatedAttributes = $request->validate([
            'title' => ['required', 'min:3', 'max:100'],
            'body' => ['required', 'min:3', 'max:225'],
            'status' => ['required'],
            'thumbnail' => ['required', File::types(['png', 'jpg', 'jpeg'])->max(10240)],
            'categories' => ['required'],
        ]);

        $thumbnailPath = $request->thumbnail->store('thumbnail', 'public');
        // $profilePhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
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

        return view('posts.show', [
            'post' => $post,
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
    public function update(Request $request, Posts $post)
    {
        $validatedAttributes = $request->validate([
            'title' => ['required', 'min:3', 'max:100'],
            'body' => ['required', 'min:3', 'max:225'],
            'status' => ['required'],
            'thumbnail' => [ File::types(['png', 'jpg', 'jpeg'])->max(10240)],
            'categories' => ['required'],
        ]);

        if ($request->hasFile('thumbnail')) {
            Storage::delete($post->thumbnail);
            $thumbnailPath = $request->thumbnail->store('thumbnail', 'public');
            $validatedAttributes['thumbnail'] = $thumbnailPath;
        }

        $post->update(Arr::except($validatedAttributes, 'categories'));

        $existingTags = explode(',', $post->tags->pluck('name')->implode(','));
        $newTags      = explode(',', $validatedAttributes['categories']);

        $deletedTags = array_diff($existingTags, $newTags);
        $addedTags   = array_diff($newTags, $existingTags);

        foreach ($deletedTags as $tag) {
            $post->untag($tag);
        }

        foreach ($addedTags as $tag) {
            $post->tag($tag);
        }

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

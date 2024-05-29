<?php

namespace App\Services;

use App\Models\Posts;
use App\Models\Tags;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostService
{
    protected $postRepository;

    public function __construct(
        PostRepository $postRepository,
    ) {
        $this->postRepository = $postRepository;
    }

    public function tagsSync($validatedTags, $post)
    {
        $tagNames = explode(',', $validatedTags);
        $tagIds = [];

        foreach ($tagNames as $name) {
            $tag = Tags::firstOrCreate(['name' => trim($name)]);
            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);
    }

    public function replaceThumbnail($thumbnail, $post)
    {
        Storage::delete($post->thumbnail);
        $thumbnailPath = $thumbnail->store('thumbnail', 'public');
        return $thumbnailPath;
    }

    public function attachTags($post, $tags)
    {
        foreach (explode(',', $tags) as $tag) {
            $post->tag($tag);
        }
    }

    public function getPostAuthors()
    {
        $cacheKey = 'postAuthors';
        return Cache::remember($cacheKey, 60, function () {
            return User::postAuthors()->get();
        });
    }

    public function getTags()
    {
        $cacheKey = 'tags';
        return Cache::remember($cacheKey, 60, function () {
            return Tags::all();
        });
    }

    public function getPostsBasedOnUser()
    {
        if (!Auth::check()) {
            return Posts::publishedWithDetails();
        }

        $authUser = Auth::user();

        if (User::isAdmin()) {
            return Posts::allWithDetails();
        }

        return $this->postRepository->getUsersOwnedAndPublishedPosts($authUser->id);
    }

}

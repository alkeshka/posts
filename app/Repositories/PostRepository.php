<?php

namespace App\Repositories;

use App\Models\Posts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

interface PostRepositoryInterface
{
    public function getPostsBasedOnUser();
}

class PostRepository implements PostRepositoryInterface
{
    // inject Post model in the custructor

    /**
     * Retrieves the posts owned and published by the authenticated user, or all published posts if the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of posts.
     */
    public function getPostsBasedOnUser()
    {
        if (!Auth::check()) {
            return Posts::publishedWithDetails();
        }

        $authUserId = Auth::id();

        if (User::isAdmin()) {
            return Posts::allWithDetails();
        }

        return Posts::allWithDetails()->where(function ($query) use ($authUserId) {
            $query->where('user_id', $authUserId)->orWhere('status', 1);
        });
    }

    /**
     * Creates a new post using the provided validated attributes.
     *
     * @param array $validatedAttributes The attributes to create the post with.
     * @return \App\Models\Posts The newly created post.
     */
    public function createPost($validatedAttributes)
    {
        return Posts::create($validatedAttributes);
    }
    
    /**
     * Updates a post with the provided attributes.
     *
     * @param Posts $post The post to be updated.
     * @param array $attributes The attributes to update the post with.
     * @return bool Returns true if the post was successfully updated, false otherwise.
     */
    public function updatePost(Posts $post, array $attributes)
    {
        return $post->update($attributes);
    }


    public function getPosts($id)
    {
        //return post find of 1
    }
}

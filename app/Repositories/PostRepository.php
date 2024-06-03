<?php

namespace App\Repositories;

use App\Models\Posts;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

interface PostRepositoryInterface
{
    public function fetchPostsBasedOnUser();
}

class PostRepository implements PostRepositoryInterface
{
    protected $postModel;

    /**
     * Constructs a new instance of the class.
     *
     * @param Posts $post The Posts model instance.
     */
    public function __construct(
        Posts $post,
    ) {
        $this->postModel = $post;
    }

    /**
     * Retrieves the posts owned and published by the authenticated user, or all published posts if the user is an admin.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of posts.
     */
    public function fetchPostsBasedOnUser()
    {
        if (!Auth::check()) {
            return $this->postModel->publishedWithDetails();
        }

        $authUserId = Auth::id();

        if (User::isAdmin()) {
            return $this->postModel->allWithDetails();
        }

        return $this->postModel->allWithDetails()->where(function ($query) use ($authUserId) {
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
        return $this->postModel->create($validatedAttributes);
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

    /**
     * Applies sorting to the given post lists based on the provided request.
     *
     * @param mixed $postLists The post lists to apply sorting to.
     * @param \Illuminate\Http\Request $request The request object containing the sorting parameters.
     * @return mixed The post lists with sorting applied.
     */
    public function applySorting($postLists, $request)
    {
        $columns = ['id', 'title', 'user.first_name', 'comments_count', 'tags', 'created_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $order = $columns[$orderColumnIndex];
        $dir = $request->input('order.0.dir', 'desc');

        if ($order === 'user.first_name') {
            $postLists = $postLists->join('users', 'posts.user_id', '=', 'users.id')
            ->orderBy('users.first_name', $dir)
                ->orderBy('users.last_name', $dir);
        } else {
            $postLists = $postLists->orderBy($order, $dir);
        }

        return $postLists;
    }

    /**
     * Retrieves a paginated list of posts based on the given request and query builder.
     *
     * @param Request $request The HTTP request object containing the pagination parameters.
     * @param Builder $postLists The query builder for the post list.
     * @return Collection The paginated list of posts.
     */
    public function paginatePosts($request, $postLists)
    {
        $limit = $request->input('length');
        $start = $request->input('start');
        $posts = $postLists->offset($start)
                    ->limit($limit)
                    ->get();

        return $posts;
    }
    
    /**
     * Retrieves the authors of posts based on the search term.
     *
     * @param string $searchTerm The search term to filter the authors by.
     * @return \Illuminate\Database\Eloquent\Collection The collection of authors matching the search term.
     */
    public function getPostAuthorsBySearchTerm($searchTerm)
    {
        return User::whereHas('posts', function ($query) use ($searchTerm) {
            $query->where('first_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('last_name', 'like', '%' . $searchTerm . '%');
        })
        ->select('id', 'first_name', 'last_name')
        ->get();
    }

}

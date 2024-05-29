<?php

namespace App\Services;

use App\Models\Posts;
use App\Models\Tags;
use App\Models\User;
use App\Repositories\PostRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostService
{
    protected $postRepository;
    protected $filterService;
    
    /**
     * Constructs a new instance of the class.
     *
     * @param PostRepository $postRepository The repository for posts.
     * @param FilterService $filterService The service for filtering.
     */
    public function __construct(
        PostRepository $postRepository,
        FilterService $filterService
    ) {
        $this->postRepository = $postRepository;
        $this->filterService = $filterService;
    }

    /**
     * Synchronizes the tags of a post with the provided validated tags.
     *
     * @param string $validatedTags The comma-separated list of validated tags.
     * @param Post $post The post to synchronize the tags with.
     * @return void
     */
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

    /**
     * Replaces the thumbnail of a post with a new thumbnail.
     *
     * @param mixed $thumbnail The new thumbnail to replace the existing one.
     * @param mixed $post The post whose thumbnail needs to be replaced.
     * @return string The path of the newly stored thumbnail.
     */
    public function replaceThumbnail($thumbnail, $post)
    {
        Storage::delete($post->thumbnail);
        $thumbnailPath = $thumbnail->store('thumbnail', 'public');
        return $thumbnailPath;
    }

    /**
     * Attaches tags to a given post.
     *
     * @param mixed $post The post to attach tags to.
     * @param string $tags The tags to attach, separated by commas.
     * @return void
     */
    public function attachTags($post, $tags)
    {
        foreach (explode(',', $tags) as $tag) {
            $post->tag($tag);
        }
    }

    /**
     * Retrieves the authors of posts from the cache or the database if not present.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of post authors.
     */
    public function getPostAuthors()
    {
        $cacheKey = 'postAuthors';
        return Cache::remember($cacheKey, 60, function () {
            return User::postAuthors()->get();
        });
    }

    /**
     * Retrieves the tags from the cache or database if not present.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of tags.
     */
    public function getTags()
    {
        $cacheKey = 'tags';
        return Cache::remember($cacheKey, 60, function () {
            return Tags::all();
        });
    }

    /**
     * Deletes a post along with its associated tags.
     *
     * @param Posts $post The post to be deleted.
     * @return void
     */
    public function deletePostWithTags(Posts $post): void
    {
        $post->tags()->detach();
        $post->delete();
    }

    /**
     * Applies sorting to the given post list based on the request parameters.
     *
     * @param Request $request The HTTP request object containing the sorting parameters.
     * @param Builder $postLists The query builder for the post list.
     * @return Builder The modified query builder with the applied sorting.
     */
    public function applySorting($request, $postLists)
    {
        $columns = ['id', 'title', 'user.first_name', 'comments_count', 'tags', 'created_at', 'actions'];
        $orderColumnIndex = $request->input('order.0.column', 0);
        $order = $columns[$orderColumnIndex];
        $dir = $request->input('order.0.dir', 'desc');

        if ($order === 'user.first_name') {
            $postLists->join('users', 'posts.user_id', '=', 'users.id')
            ->orderBy('users.first_name', $dir)
            ->orderBy('users.last_name', $dir);
        } else {
            $postLists->orderBy($order, $dir);
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
    public function getPaginate($request, $postLists)
    {
        $limit = $request->input('length');
        $start = $request->input('start');
        $posts = $postLists->offset($start)
            ->limit($limit)
            ->get();

        return $posts;
    }

    /**
     * Formats the given posts data into a nested array structure for display.
     *
     * @param Collection $posts The collection of posts to format.
     * @return array The formatted posts data.
     */
    public function getFormatData($posts)
    {
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

        return $data;
    }

    /**
     * Creates a JSON response with the given data.
     *
     * @param mixed $request The HTTP request object.
     * @param int $totalPostsCount The total count of posts.
     * @param int $filteredPostsCount The count of posts after applying filters.
     * @param array $formattedData The formatted data to be included in the response.
     * @return array The JSON response with the following keys:
     * - "draw": The value of the 'draw' parameter from the request.
     * - "recordsTotal": The total count of posts.
     * - "recordsFiltered": The count of posts after applying filters.
     * - "data": The formatted data to be included in the response.
     */
    public function createJsonResponse($request, $totalPostsCount, $filteredPostsCount, $formattedData)
    {
        return [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalPostsCount),
            "recordsFiltered" => intval($filteredPostsCount),
            "data"            => $formattedData
        ];
    }
}

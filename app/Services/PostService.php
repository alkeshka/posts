<?php

namespace App\Services;

use App\Helpers\AuthHelper;
use App\Models\Posts;
use App\Models\Tags;
use App\Models\User;
use App\Repositories\PostRepository;
use App\Repositories\TagRepository;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostService
{
    protected $postRepository;
    protected $filterService;
    protected $authHelper;
    protected $tagRepository;

    /**
     * Constructs a new instance of the class.
     *
     * @param PostRepository $postRepository The repository for posts.
     * @param FilterService $filterService The service for filtering.
     */
    public function __construct(
        PostRepository $postRepository,
        FilterService $filterService,
        AuthHelper $authHelper,
        TagRepository $tagRepository
    ) {
        $this->postRepository = $postRepository;
        $this->filterService  = $filterService;
        $this->authHelper = $authHelper;
        $this->tagRepository = $tagRepository;
    }

    /**
     * Synchronizes the tags of a post with the provided validated tags.
     *
     * @param string $validatedTags The comma-separated list of validated tags.
     * @param Post $post The post to synchronize the tags with.
     * @return void
     */
    public function syncTagsWithPost($validatedTags, $post)
    {
        $tagNames = explode(',', $validatedTags);
        $tagIds = $this->tagRepository->getOrCreateTags($tagNames);
        $post->tags()->sync($tagIds);
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
    public function deletePost(Posts $post): void
    {
        $post->tags()->detach();
        $post->delete();
    }

    /**
     * Retrieves the posts data based on the given request.
     *
     * @param Request $request The HTTP request object containing the sorting and filtering parameters.
     * @return JsonResponse The JSON response containing the paginated and formatted posts data.
     */
    public function getPostsData($request)
    {
        $postLists          = $this->postRepository->fetchPostsBasedOnUser();
        $totalPostsCount    = $postLists->count();
        $sortedPosts        = $this->postRepository->applySorting($postLists, $request);
        $filteredPosts      = $this->filterService->applyFilters($sortedPosts, $request);
        $paginatedPosts     = $this->postRepository->paginatePosts($request, $filteredPosts);
        $formattedData      = $this->formatData($paginatedPosts);
        $filteredPostsCount = $filteredPosts->count();

        return $this->createJsonResponse($request, $totalPostsCount, $filteredPostsCount, $formattedData);
    }

    /**
     * Formats the given posts data into a nested array structure for display.
     *
     * @param Collection $posts The collection of posts to format.
     * @return array The formatted posts data.
     */
    public function formatData($posts)
    {
        $data = array();
        if (!empty($posts)) {
            foreach ($posts as $index => $post) {
                $nestedData['id']             = $index + 1;
                $nestedData['title']          = $post->title;
                $nestedData['author']         = $post->user->first_name . ' ' . $post->user->last_name;
                $nestedData['comments_count'] = '<button onclick="loadComments(' . $post->id . ')">' . $post->comments_count . '</button>';
                $nestedData['tags']           = $post->tags->pluck('name')->implode(', ');
                $nestedData['created_at']     = $post->created_at;
                $nestedData['actions']        = $this->getActionButtons($post);

                $data[] = $nestedData;
            }
        }

        return $data;
    }

    /**
     * Generates the action buttons for a given post.
     *
     * @param Post $post The post object.
     * @return string The HTML string containing the action buttons.
     */
    protected function getActionButtons($post)
    {
        $actions = '<div class="flex justify-center items-center">';

        $actions .= '<a href="/posts/' . $post->id . '" class="font-medium text-blue-600 text-blue-500 hover:underline"><i class="fa fa-eye" style="font-size:18px"></i></a>';

        if (auth()->check() && (auth()->id() == $post->user_id || User::isAdmin())) {
            $actions .= '<a href="/posts/' . $post->id . '/edit" class="ml-2 font-medium text-blue-600 text-blue-500 hover:underline"><i class="fa fa-edit" style="font-size:18px"></i></a>';
        }

        if (auth()->check() && User::isAdmin()) {
            $actions .= '<a onclick="return confirm(\'Are you sure?\')" href="/posts/' . $post->id . '/delete" class="ml-2 font-medium text-blue-600 text-blue-500 hover:underline"><i class="fa fa-trash-o text-red-500" style="font-size:18px"></i></a>';
        }

        $actions .= '</div>';

        return $actions;
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

    /**
     * Creates a new post with the given validated attributes.
     *
     * @param array $validatedAttributes The validated attributes of the post.
     * It should contain the 'thumbnail' and 'categories' keys.
     * @return Post The newly created post.
     */
    public function createPost(array $validatedAttributes)
    {
        $validatedAttributes['thumbnail'] = $this->uploadThumbnail($validatedAttributes['thumbnail']);
        $validatedAttributes['user_id'] = $this->authHelper->getAuthenticatedUserId();

        $post = $this->postRepository->createPost(Arr::except($validatedAttributes, 'categories'));

        $this->syncCategories($validatedAttributes['categories'], $post);

        return $post;
    }

    /**
     * Uploads the given thumbnail and stores it in the 'thumbnail' directory of the public storage.
     *
     * @param mixed $thumbnail The thumbnail to be uploaded.
     * @return string The path of the uploaded thumbnail.
     */
    protected function uploadThumbnail($thumbnail)
    {
        return $thumbnail->store('thumbnail', 'public');
    }

    /**
     * Synchronizes the given categories with the given post.
     *
     * @param mixed $categories The categories to be synchronized.
     * @param mixed $post The post to be synchronized with the categories.
     * @return void
     */
    public function syncCategories($categories, $post)
    {
        if (!empty($categories)) {
            $this->syncTagsWithPost($categories, $post);
        }
    }

    /**
     * Updates a post with the given validated attributes.
     *
     * @param array $validatedAttributes The validated attributes to update the post with.
     * @param mixed $post The post to be updated.
     * @return void
     */
    public function updatePost(array $validatedAttributes, $post)
    {
        if (isset($validatedAttributes['thumbnail'])) {
            $validatedAttributes['thumbnail'] = $this->replaceThumbnail($validatedAttributes['thumbnail'], $post);
        }

        $this->postRepository->updatePost($post, Arr::except($validatedAttributes, 'categories'));

        $this->syncCategories($validatedAttributes['categories'] ?? [], $post);
    }

    /**
     * Replaces the thumbnail of a post with a new thumbnail.
     *
     * @param mixed $thumbnail The new thumbnail to replace the existing one.
     * @param Post $post The post object whose thumbnail needs to be replaced.
     * @return mixed The result of the uploadThumbnail() method.
     */
    public function replaceThumbnail($thumbnail, $post)
    {
        if ($post->thumbnail) {
            Storage::delete($post->thumbnail);
        }
        return $this->uploadThumbnail($thumbnail);
    }

    /**
     * Retrieves the posts based on the authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Collection The collection of posts.
     */
    public function getPostsBasedOnUser()
    {
        return $this->postRepository->fetchPostsBasedOnUser();
    }

}

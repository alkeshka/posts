<?php

namespace App\Services;

use App\Models\Posts;
use App\Models\Tags;
use App\Models\User;
use App\Repositories\PostRepository;
use Carbon\Carbon;
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

    public function applyFilters($request, $postLists)
    {
        if ($request->has('publishedDateRangeStart') && $request->has('publishedDateRangeEnd')) {
            $publishedDateRangeStart = $request->input('publishedDateRangeStart');
            $publishedDateRangeEnd = $request->input('publishedDateRangeEnd');

            if (!is_null($publishedDateRangeStart) && !is_null($publishedDateRangeEnd)) {
                $startDate = Carbon::parse($publishedDateRangeStart);
                $endDate = Carbon::parse($publishedDateRangeEnd);
                $postLists->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        if ($request->has('noOfComments') && $request->input('noOfComments') != '') {
            $noOfComments = $request->input('noOfComments');
            $postLists->where('comments_count', '=', (int) $noOfComments);
        }

        if ($request->has('searchQuery') && $request->input('searchQuery') != '') {
            $search = $request->input('searchQuery');
            $postLists->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('author') && $request->input('author') != '') {
            $postLists->where('user_id', $request->author);
        }

        if ($request->has('category') && $request->input('category') != '') {
            $tagId = $request->category;
            $postLists->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        return $postLists;
    }

    public function getPaginate($request, $postLists)
    {
        $limit = $request->input('length');
        $start = $request->input('start');
        $posts = $postLists->offset($start)
            ->limit($limit)
            ->get();

        return $posts;
    }

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

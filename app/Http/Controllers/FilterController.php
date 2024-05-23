<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use App\Repositories\PostRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FilterController extends Controller
{

    protected $postService;
    protected $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function __invoke(Request $request)
    {

        if (!Auth::check()) {
            $postQuery = Posts::publishedWithDetails();
        } else {
            $authUser = Auth::user();
            if ($authUser->users_role_id == 1) {
                $postQuery = Posts::allWithDetails();
            } else {
                $postQuery = $this->postRepository->getUsersOwnedAndPublishedPosts($authUser->id);
            }
        }


        if ($author = $request->author) {
            $postQuery = $postQuery->where('user_id', $author);
        }

        if ($tagId = $request->category) {
            $postQuery = $postQuery->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        if ($request->has('noOfComments') && $request->noOfComments != null) {
            $commentCount = (int) $request->noOfComments;
            if ($commentCount >= 0) {
                $postQuery = $postQuery->where('comments_count', '=', $commentCount);
            }
        }

        if (isset($request->publishedDate)) {
            $publishedDate = Carbon::createFromFormat('d/m/Y', $request->publishedDate);
            $postQuery = $postQuery->whereDate('created_at', '=', $publishedDate->toDateString());
        }

        if ($searchQuery = $request->searchQuery) {
            $postQuery = $postQuery->where('title', 'like', "%$searchQuery%");
        }

        return $postQuery->with(['tags', 'user'])->get();
    }
}

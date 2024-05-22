<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __invoke(Request $request)
    {
        $postQuery = Posts::latest()->where('status', 1)->withCount('comments');

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

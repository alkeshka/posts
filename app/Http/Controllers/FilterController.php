<?php

namespace App\Http\Controllers;

use App\Models\Posts;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __invoke(Request $request)
    {
        $posts = Posts::latest()->where('status', 1)->withCount('comments');

        if ($author = $request->author) {
            $posts = $posts->where('user_id', $author);
        }

        if ($tagId = $request->category) {
            $posts = $posts->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        if ($commentCount = $request->noOfComments) {
            $posts = $posts->having('comments_count', '=', $commentCount);
        }

        if (isset($request->publishedDate)) {
            $publishedDate = Carbon::createFromFormat('d/m/Y', $request->publishedDate);
            $posts = $posts->whereDate('created_at', '=', $publishedDate->toDateString());
        }

        if ($searchQuery = $request->searchQuery) {
            $posts = $posts->where('title', 'like', "%$searchQuery%");
        }

        return $posts->with(['tags', 'user'])->get();
    }
}

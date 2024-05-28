<?php

namespace App\Http\Controllers;

use App\Models\Tags;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    /**
     * Retrieves the posts associated with a given tag.
     *
     * @param Tags $tag The tag object.
     * @return View The view displaying the posts associated with the tag.
     */
    public function __invoke(Tags $tags): View
    {
        $associatedPosts = $tags->posts()->simplePaginate();
        return view('tags.posts', compact('tags', 'associatedPosts'));
    }
}

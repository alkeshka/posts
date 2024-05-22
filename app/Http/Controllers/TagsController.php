<?php

namespace App\Http\Controllers;

use App\Models\Tags;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function __invoke(Tags $tags)
    {
        $associatedPosts = $tags->posts()->simplePaginate();
        return view('tags.posts', compact('tags', 'associatedPosts'));
    }
}

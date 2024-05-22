<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class PostService
{
    public function tagsSync($validatedTags, $post)
    {
        $existingTags = explode(',', $post->tags->pluck('name')->implode(','));
        $currentTags  = explode(',', $validatedTags);

        $deletedTags = array_diff($existingTags, $currentTags);
        $addedTags   = array_diff($currentTags, $existingTags);

        foreach ($deletedTags as $tag) {
            $post->untag($tag);
        }

        foreach ($addedTags as $tag) {
            $post->tag($tag);
        }
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
}

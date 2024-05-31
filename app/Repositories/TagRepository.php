<?php

namespace App\Repositories;

use App\Models\Tags;

class TagRepository
{
    private $tagModel;

    /**
     * Constructs a new instance of the class.
     *
     * @param Tags $tag The tag model to be used by the class.
     */
    public function __construct(
        Tags $tag,
    ) {
        $this->tagModel = $tag;
    }

    /**
     * Retrieves or creates tags based on an array of tag names.
     *
     * @param array $tagNames The array of tag names.
     * @return array The array of tag IDs.
     */
    public function getOrCreateTags(array $tagNames)
    {
        $tagIds = [];
        foreach ($tagNames as $name) {
            $tag = $this->tagModel->firstOrCreate(['name' => trim($name)]);
            $tagIds[] = $tag->id;
        }
        return $tagIds;
    }
}

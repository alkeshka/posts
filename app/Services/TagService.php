<?php

namespace App\Services;

use App\Repositories\TagRepository;

class TagService
{
    protected $tagRepository;
    
    /**
     * Constructs a new instance of the class.
     *
     * @param TagRepository $tagRepository The repository for managing tags.
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * Get tags based on the search term.
     *
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTags(string $searchTerm)
    {
        return $this->tagRepository->getTagsBySearchTerm($searchTerm);
    }
}

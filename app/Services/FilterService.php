<?php

namespace App\Services;



class FilterService
{

    protected $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    public function applyFilters($postQuery, $request)
    {
        $postQuery = $this->applyAuthorFilter($postQuery, $request->author);
        $postQuery = $this->applyTagFilter($postQuery, $request->category);
        $postQuery = $this->applyCommentCountFilter($postQuery, $request->noOfComments);
        $postQuery = $this->applyPublishedDateFilter($postQuery, $request->publishedDate);
        $postQuery = $this->applySearchQueryFilter($postQuery, $request->searchQuery);

        return $postQuery;
    }

    public function applyAuthorFilter($postQuery, $author)
    {
        if ($author) {
            return $postQuery->where('user_id', $author);
        }

        return $postQuery;
    }

    public function applyTagFilter($postQuery, $tagId)
    {
        if ($tagId) {
            return $postQuery->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        }

        return $postQuery;
    }

    public function applyCommentCountFilter($postQuery, $commentCount)
    {
        if ($commentCount !== null && $commentCount >= 0) {
            return $postQuery->where('comments_count', '=', (int) $commentCount);
        }

        return $postQuery;
    }

    public function applyPublishedDateFilter($postQuery, $publishedDate)
    {
        if ($publishedDate) {
            $formattedDate = $this->dateService->parseDate($publishedDate);
            return $postQuery->whereDate('created_at', '=', $formattedDate);
        }

        return $postQuery;
    }

    public function applySearchQueryFilter($postQuery, $searchQuery)
    {
        if ($searchQuery) {
            return $postQuery->where('title', 'like', "%$searchQuery%");
        }

        return $postQuery;
    }
}

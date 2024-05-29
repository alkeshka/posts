<?php

namespace App\Services;

use Carbon\Carbon;

class FilterService
{

    /**
     * Applies various filters to the given post query based on the request parameters.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $request The request object containing the filter parameters.
     * @return mixed The filtered post query.
     */
    public function applyFilters($postQuery, $request)
    {
        $postQuery = $this->applyAuthorFilter($postQuery, $request->author);
        $postQuery = $this->applyTagFilter($postQuery, $request->category);
        $postQuery = $this->applyCommentCountFilter($postQuery, $request->noOfComments);
        $postQuery = $this->applySearchQueryFilter($postQuery, $request->searchQuery);
        $postQuery = $this->applyPublishedDateFilter($postQuery, $request->publishedDateRangeStart, $request->publishedDateRangeEnd);

        return $postQuery;
    }

    /**
     * Apply the author filter to the post query.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $author The author ID to filter by. If null or empty string, no filter is applied.
     * @return mixed The filtered post query.
     */
    public function applyAuthorFilter($postQuery, $author)
    {
        if ($author && $author != '') {
            $postQuery->where('user_id', $author);
        }
        return $postQuery;
    }

    /**
     * Apply the tag filter to the post query.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $category The category Name to filter by. If null or empty string, no filter is applied.
     * @return mixed The filtered post query.
     */

    public function applyTagFilter($postQuery, $category)
    {
        if ($category !== null && $category !== '') { // Ensure $category is not null and not an empty string
            $postQuery->whereHas('tags', function ($q) use ($category) {
                $q->where('tags.id', $category);
            });
        }
        return $postQuery;
    }

    /**
     * Apply the comment count filter to the post query.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $noOfComments The comment count to filter by. If null or empty string, no filter is applied.
     * @return mixed The filtered post query.
     */
    public function applyCommentCountFilter($postQuery, $noOfComments)
    {
        if ($noOfComments && $noOfComments != '') {
            $noOfComments = $noOfComments;
            $postQuery->where('comments_count', '=', (int) $noOfComments);
        }
        return $postQuery;
    }

    /**
     * Apply the search query filter to the post query.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param string|null $searchQuery The search query to filter by. If null or empty string, no filter is applied.
     * @return mixed The filtered post query.
     */
    public function applySearchQueryFilter($postQuery, $searchQuery)
    {
        if ($searchQuery && $searchQuery != '') {
            $search = $searchQuery;
            $postQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%");
            });
        }

        return $postQuery;
    }

    /**
     * Apply the published date filter to the post query.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $publishedDateRangeStart The start date of the published date range.
     * @param mixed $publishedDateRangeEnd The end date of the published date range.
     * @return mixed The filtered post query.
     */
    public function applyPublishedDateFilter($postQuery, $publishedDateRangeStart, $publishedDateRangeEnd)
    {
        if (!is_null($publishedDateRangeStart) && !is_null($publishedDateRangeEnd)) {
            $startDate = Carbon::parse($publishedDateRangeStart);
            $endDate = Carbon::parse($publishedDateRangeEnd);
            $postQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        return $postQuery;
    }
}

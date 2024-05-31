<?php

namespace App\Repositories;

use Carbon\Carbon;

class FilterRepository
{
    /**
     * Apply the author filter to the post query.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $author The author ID to filter by. If null or empty string, no filter is applied.
     * @return mixed The filtered post query.
     */
    public function applyAuthorFilter($postQuery, $authors)
    {
        if (!empty($authors)) {
            $postQuery->whereIn('user_id', $authors);
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

    public function applyTagFilter($postQuery, $tagIds)
    {
        if (!empty($tagIds)) {
            foreach ($tagIds as $tagId) {
                $postQuery->whereHas('tags', function ($q) use ($tagId) {
                    $q->where('tags.id', $tagId);
                });
            }
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
        if ($noOfComments !== null && $noOfComments !== '') {
            $noOfComments = (int) $noOfComments;
            $postQuery->where('comments_count', '=', $noOfComments);
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

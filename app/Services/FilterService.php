<?php

namespace App\Services;

use Carbon\Carbon;

class FilterService
{
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
            $formattedDate = Carbon::createFromFormat('d/m/Y', $publishedDate)->toDateString();
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

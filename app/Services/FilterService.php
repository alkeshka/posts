<?php

namespace App\Services;

use App\Repositories\FilterRepository;
use Carbon\Carbon;

class FilterService
{
    private $filterRepository;
    public function __construct(FilterRepository $filterRepository)
    {
        $this->filterRepository = $filterRepository;
    }

    /**
     * Applies various filters to the given post query based on the request parameters.
     *
     * @param mixed $postQuery The initial post query to be filtered.
     * @param mixed $request The request object containing the filter parameters.
     * @return mixed The filtered post query.
     */
    public function applyFilters($postQuery, $request)
    {
        $postQuery = $this->filterRepository->applyAuthorFilter($postQuery, $request->author);
        $postQuery = $this->filterRepository->applyTagFilter($postQuery, $request->category);
        $postQuery = $this->filterRepository->applyCommentCountFilter($postQuery, $request->noOfComments);
        $postQuery = $this->filterRepository->applySearchQueryFilter($postQuery, $request->searchQuery);
        $postQuery = $this->filterRepository->applyPublishedDateFilter($postQuery, $request->publishedDateRangeStart, $request->publishedDateRangeEnd);

        return $postQuery;
    }

}

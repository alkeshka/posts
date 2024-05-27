<?php

namespace App\Repositories;

use App\Models\Posts;
use App\Services\DateService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    public function getFormattedPublishedDates(): Collection;
}

class PostRepository implements PostRepositoryInterface
{
    protected $dateService;

    public function __construct(DateService $dateService)
    {
        $this->dateService = $dateService;
    }

    public function getFormattedPublishedDates(): Collection
    {
        return Posts::where('status', 1)
            ->pluck('created_at')
            ->map(function ($date) {
                // return Carbon::parse($date)->format('d/m/Y');
                return $this->dateService->formatDate($date);
            })
            ->unique()
            ->values();
    }

    public function getPostsCommentsCounts()
    {
        return Posts::withCount('comments')->orderBy('comments_count', 'asc')->pluck('comments_count')->unique()->values();
    }

    public function getUsersOwnedAndPublishedPosts($userId)
    {
        return Posts::allWithDetails()->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('status', 1);
                });
    }
}

<?php

namespace App\Repositories;

use App\Models\Posts;
use Carbon\Carbon;
use Illuminate\Support\Collection;

interface PostRepositoryInterface
{
    public function getFormattedPublishedDates(): Collection;
}

class PostRepository implements PostRepositoryInterface
{
    public function getFormattedPublishedDates(): Collection
    {
        return Posts::where('status', 1)
            ->pluck('created_at')
            ->map(function ($date) {
                return Carbon::parse($date)->format('d/m/Y');
            })
            ->unique()
            ->values();
    }

    public function getPostsCommentsCounts()
    {
        return Posts::withCount('comments')->orderBy('comments_count', 'asc')->pluck('comments_count')->unique()->values();
    }
}

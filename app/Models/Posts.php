<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posts extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'body',
        'status',
        'user_id',
        'thumbnail',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'boolean',
        'user_id' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class);
    }

    public function tag(string $tagName)
    {
        $tag = Tags::firstOrCreate(['name' => strtolower($tagName)]);

        $this->tags()->attach($tag);

    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    public function untag()
    {
        $this->tags()->detach();
    }

    public function scopePublishedWithDetails()
    {
        return $this->latest()->where('status', 1)->with(['tags', 'user'])->withCount('comments');
    }

    public function scopePostsCommentsCounts()
    {
        return $this->withCount('comments')->orderBy('comments_count', 'asc')->pluck('comments_count')->unique()->values();
    }

    public function scopeFormattedPublishedDates()
    {
        return $this->where('status', 1)
                    ->pluck('created_at')
                    ->map(function ($date) {
                        return Carbon::parse($date)->format('d/m/Y');
                    })
                    ->unique()
                    ->values();
    }

}

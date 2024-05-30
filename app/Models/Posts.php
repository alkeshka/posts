<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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

    /**
     * Returns an Attribute object that formats the createdAt value as 'd/m/Y'.
     *
     * @return Attribute The formatted createdAt value as an Attribute object.
     */
    protected function createdAt(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => \Carbon\Carbon::parse($value)->format('d/m/Y'),
        );
    }

    /**
     * Retrieve the user that belongs to this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the tags associated with this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany The tags relationship.
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tags::class);
    }

    /**
     * Associates a tag with the current post.
     * If the tag doesn't already exist, it will be created.
     *
     * @param string $tagName The name of the tag to associate.
     *
     * @return void
     */
    public function tag(string $tagName)
    {
        $tag = Tags::firstOrCreate(['name' => strtolower($tagName)]);

        $this->tags()->attach($tag);

    }

    /**
     * Returns a HasMany relationship instance for the comments associated with this post.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    /**
     * Removes a tag from the current post.
     *
     * @param string $tagName The name of the tag to remove.
     * @return void
     */
    public function untag($tagName)
    {
        $tag = Tags::where('name', $tagName)->first();
        if ($tag) {
            $this->tags()->detach($tag->id);
        }
    }

    /**
     * Scope a query to only include published posts with their associated tags and user,
     * and the count of comments.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublishedWithDetails()
    {
        return $this->where('status', 1)->with(['tags', 'user'])->withCount('comments');
    }

    /**
     * Scope a query to include all posts with their associated tags and users,
     * along with the count of comments for each post.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAllWithDetails()
    {
        return $this->with(['tags', 'user'])->withCount('comments');
    }

}

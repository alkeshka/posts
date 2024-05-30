<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comments extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'body',
        'user_id',
        'posts_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'posts_id' => 'integer',
    ];

    /**
     * Retrieve the user that belongs to this comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The user relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Retrieve the post that belongs to this comment.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The post relationship.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Posts::class);
    }

}

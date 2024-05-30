<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostsTags extends Model
{
    use HasFactory;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'post_id',
        'tag_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'post_id' => 'integer',
        'tag_id' => 'integer',
    ];

    /**
     * Retrieve the associated post model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The post relationship.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Posts::class);
    }

    /**
     * Retrieve the associated tag model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo The tag relationship.
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tags::class);
    }
}

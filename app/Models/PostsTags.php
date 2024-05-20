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

    public function post(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Posts::class);
    }

    public function tag(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tags::class);
    }
}

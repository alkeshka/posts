<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    /**
     * Retrieve the posts associated with this tag.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany The posts relationship.
     */
    public function posts()
    {
        return $this->belongsToMany(Posts::class);
    }

}

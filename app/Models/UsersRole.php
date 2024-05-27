<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersRole extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_type',
        'role_name',
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
     * Retrieve the ID of the normal user role.
     *
     * @return int
     */
    public static function getNormalUserRoleTypeId()
    {
        return UsersRole::where('role_type', User::ROLE_USER)->value('id');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;

    const ROLE_ADMIN = 1;
    const ROLE_USER  = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'users_role_type_id',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Posts::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    public function scopePostAuthors()
    {
        return $this->whereHas('posts');
    }

    /**
     * Check if the authenticated user is an admin.
     *
     * @return bool
     */
    public function scopeIsAdmin()
    {
        return Auth::user()->users_role_type_id == User::ROLE_ADMIN;
    }
}

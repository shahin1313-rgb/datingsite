<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Like extends Model
{
    use HasFactory;

    // فیلدهایی که اجازه mass assignment دارند
    protected $fillable = [
        'user_id',        // کاربری که لایک کرده
        'liked_user_id',  // کاربری که لایک شده
    ];

    /**
     * کاربری که لایک کرده
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * کاربری که لایک شده
     */
    public function likedUser()
    {
        return $this->belongsTo(User::class, 'liked_user_id');
    }


    /**
     * Get the users that this user has liked.
     */
    public function likes()
    {
        return $this->hasMany(Like::class, 'user_id');
    }

    /**
     * Get the users who have liked this user.
     */
    public function likedBy()
    {
        return $this->hasMany(Like::class, 'liked_user_id');
    }
}

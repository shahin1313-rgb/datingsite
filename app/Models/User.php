<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'gender',
        'visits_count',
        'city',
        'bio',
        'profile_picture',
        'age',
        'marital_status',
        'birth_year',
        'role',
        'interested_in',
        'salary',

    ];
    // protected $attributes = [
    //     'date_of_birth' => '1990-01-01',
    // ];

    // Define relationships
    public function matches()
    {
        return $this->hasMany(MatchUser::class, 'user_id');
    }

    // Relationship to sent messages
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Relationship to received messages
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }
    public function isAdmin()
    {
        return $this->role === 'admin'; // فرض بر اینکه ستون `role` دارید
    }

    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }



    public function unreadMessagesCount()
    {
        return $this->receivedMessages()->whereNull('read_at')->count();
    }

    public function reportsMade()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function reportsReceived()
    {
        return $this->hasMany(Report::class, 'reported_id');
    }

    public function blockedUsers()
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function hasBlocked($userId)
    {
        return $this->blockedUsers()->where('blocked_id', $userId)->exists();
    }

    public function isBlockedBy($userId)
    {
        return Block::where('blocker_id', $userId)->where('blocked_id', $this->id)->exists();
    }

    public function profileViewsReceived()
    {
        return $this->hasMany(ProfileView::class, 'viewed_id')->latest();
    }

    public function profileViewsGiven()
    {
        return $this->hasMany(ProfileView::class, 'viewer_id')->latest();
    }
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'user_id', 'liked_user_id');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'likes', 'liked_user_id', 'user_id');
    }


    public function receivedLikes()
{
    return $this->hasMany(Like::class, 'liked_user_id');
}

public function sentLikes()
{
    return $this->hasMany(Like::class, 'user_id');
}


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
}

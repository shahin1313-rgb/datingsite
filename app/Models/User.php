<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * فیلدهای قابل مقداردهی.
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
        'premium_until',
        'created_at',
        'updated_at',
        'is_premium',
        'last_crypto_hash',
    ];

    public function matches()
    {
        return $this->hasMany(MatchUser::class, 'user_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function unreadMessagesCount()
    {
        return $this->receivedMessages()
            ->whereNull('read_at')
            ->count();
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
        return $this->blockedUsers()
            ->where('blocked_id', $userId)
            ->exists();
    }

    public function isBlockedBy($userId)
    {
        return Block::where('blocker_id', $userId)
            ->where('blocked_id', $this->id)
            ->exists();
    }

    public function profileViewsReceived()
    {
        return $this->hasMany(
            ProfileView::class,
            'viewed_id'
        )->latest();
    }

    public function profileViewsGiven()
    {
        return $this->hasMany(
            ProfileView::class,
            'viewer_id'
        )->latest();
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function likedUsers()
    {
        return $this->belongsToMany(
            User::class,
            'likes',
            'user_id',
            'liked_user_id'
        )->withTimestamps();
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(
            User::class,
            'likes',
            'liked_user_id',
            'user_id'
        )->withTimestamps();
    }

    public function receivedLikes()
    {
        return $this->hasMany(
            Like::class,
            'liked_user_id'
        );
    }

    public function sentLikes()
    {
        return $this->hasMany(
            Like::class,
            'user_id'
        );
    }

    /**
     * بررسی فعال‌بودن پریمیوم.
     */
    public function isPremium(): bool
    {
        return $this->premium_until !== null
            && $this->premium_until->isFuture();
    }

    /**
     * اعطای پریمیوم برای تعداد مشخصی روز.
     */
    public function grantPremium(int $days = 30)
    {
        $now = Carbon::now();

        if (
            $this->premium_until &&
            $this->premium_until->isFuture()
        ) {
            $this->premium_until =
                $this->premium_until->addDays($days);
        } else {
            $this->premium_until = $now->addDays($days);
        }

        $this->save();
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * فیلدهای مخفی.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * تبدیل نوع فیلدها.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'premium_until' => 'datetime',
        ];
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserProfileVisit extends Model
{
     use HasFactory;
     protected $table = 'user_profile_visits';

    protected $fillable = [
        'viewer_id',
        'profile_owner_id','created_at'
    ];

    public function viewer()
    {
        return $this->belongsTo(User::class, 'viewer_id');
    }
}

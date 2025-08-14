<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Like extends Model
{
    protected $fillable = [
        'user_id',
        'liked_user_id',
    ];
}

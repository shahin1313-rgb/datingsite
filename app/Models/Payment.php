<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
     protected $fillable = [
        'user_id', 'gateway', 'gateway_transaction_id', 'amount', 'status', 'meta'
    ];

    protected $casts = [
        'meta' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PremiumPaymentIntent extends Model
{
    protected $fillable = [
        'public_id',
        'user_id',
        'reference_code',
        'expected_amount_atomic',
        'expires_at',
        'consumed_at',
    ];

    protected function casts(): array
    {
        return [
            'reference_code' => 'integer',
            'expires_at' => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(PremiumPayment::class);
    }
}

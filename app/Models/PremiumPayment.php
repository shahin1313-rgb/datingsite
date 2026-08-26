<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PremiumPayment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_intent_id',
        'network',
        'chain_id',
        'asset',
        'token_contract',
        'tx_hash',
        'sender_address',
        'receiver_address',
        'amount_atomic',
        'block_number',
        'confirmations',
        'status',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'chain_id' => 'integer',
            'block_number' => 'integer',
            'confirmations' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentIntent(): BelongsTo
    {
        return $this->belongsTo(PremiumPaymentIntent::class);
    }
}

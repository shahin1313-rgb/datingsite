<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'message','read_at'];

   // Relationship to sender
   public function sender()
   {
       return $this->belongsTo(User::class, 'sender_id');
   }

   // Relationship to receiver
   public function receiver()
   {
       return $this->belongsTo(User::class, 'receiver_id');
   }
}

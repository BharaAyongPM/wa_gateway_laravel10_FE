<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'device_id',
        'sender',
        'recipient',
        'recipient_type',
        'message_type',
        'content',
        'media_url',
        'status',
        'provider',
        'error_message',
        'retry_count',
        'scheduled_at',
        'sent_at',
        'meta'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'meta' => 'array',
    ];

    public function device()
    {
        return $this->belongsTo(\App\Models\Device::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

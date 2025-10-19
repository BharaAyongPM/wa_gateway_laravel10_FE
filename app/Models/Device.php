<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'session_id',
        'status',
        'qr_code',
        'last_connected_at',
        'api_key',
        'user_id',
        'is_trial',
        'quota',
        'messages_sent',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function messages()
    {
        return $this->hasMany(\App\Models\Message::class);
    }

    // Subscription aktif utk device ini
    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class)
            ->where('expired_at', '>=', now())
            ->latestOfMany(); // ambil yang terbaru kalau ada lebih dari satu
    }
}

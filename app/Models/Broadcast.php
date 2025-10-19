<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;
    protected $fillable = ['message', 'send_time', 'groups', 'active', 'device_id', 'user_id'];

    protected $casts = [
        'groups' => 'array',
        'send_time' => 'datetime:H:i',
        'active' => 'boolean',
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

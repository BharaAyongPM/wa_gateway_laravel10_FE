<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoReply extends Model
{
    use HasFactory;
    protected $fillable = ['keyword', 'response', 'type', 'device_id', 'user_id', 'active'];

    public function device()
    {
        return $this->belongsTo(\App\Models\Device::class);
    }
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}

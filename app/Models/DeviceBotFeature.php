<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceBotFeature extends Model
{
    use HasFactory;
    protected $table = 'device_bot_features';
    protected $fillable = ['device_id', 'feature_key', 'enabled'];
    public $incrementing = false;
    protected $primaryKey = null;
    protected $casts = [
        'enabled' => 'boolean',
    ];
}

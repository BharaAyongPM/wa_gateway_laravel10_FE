<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceBot extends Model
{
    use HasFactory;
    protected $fillable = ['device_id', 'is_enabled'];
    public function device()
    {
        return $this->belongsTo(Device::class);
    }
    public function features()
    {
        return $this->hasMany(DeviceBotFeature::class, 'device_id', 'device_id');
    }
}

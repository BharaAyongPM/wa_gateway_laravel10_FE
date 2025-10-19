<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'price',
        'duration',
        'max_devices',
        'quota_limit',
        'can_image',
        'can_pdf',
        'can_autoreply',
    ];

    // Relasi ke subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}

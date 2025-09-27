<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'device_id',        // <— WAJIB
        'plan_id',
        'started_at',
        'expired_at',
        'remaining_quota',
        'is_trial',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expired_at' => 'datetime',
        'is_trial'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function device()
    {
        return $this->belongsTo(\App\Models\Device::class);
    }
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // subscription aktif (belum lewat masa berlaku)
    public function scopeActive($q)
    {
        return $q->where('expired_at', '>=', now());
    }

    public function isActive(): bool
    {
        return !!$this->expired_at && $this->expired_at->isFuture();
    }

    public function daysRemaining(): int
    {
        return $this->expired_at ? now()->diffInDays($this->expired_at, false) : 0;
    }

    // Helper tampilan nama plan
    public function displayName(): string
    {
        return $this->is_trial ? 'Trial' : ($this->plan->name ?? 'Custom');
    }
}

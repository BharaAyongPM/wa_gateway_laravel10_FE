<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BotFeature extends Model
{
    use HasFactory;
    protected $fillable = ['key', 'name', 'description'];
}

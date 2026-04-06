<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = ['ip_address', 'user_agent', 'last_active_at'];

    protected $casts = [
        'last_active_at' => 'datetime',
    ];

    /**
     * Scope to only include active visitors in the last N minutes
     */
    public function scopeActive($query, $minutes = 5)
    {
        return $query->where('last_active_at', '>=', now()->subMinutes($minutes));
    }
}

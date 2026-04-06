<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'type',
        'target',
        'scheduled_at',
        'is_dispatched',
        'title',
        'message',
        'link',
        'read_at',
        'notifiable_id',
        'notifiable_type',
        'user_id',
        'data',
        'priority',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'is_dispatched' => 'boolean',
        'data' => 'array',
    ];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function isRead()
    {
        return $this->read_at !== null;
    }

    public function markAsRead()
    {
        $this->update(['read_at' => now()]);
    }
}

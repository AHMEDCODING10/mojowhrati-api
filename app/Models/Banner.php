<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image_url',
        'title',
        'type',
        'video_url',
        'target',
        'placement',
        'link',
        'is_active',
        'position',
        'start_at',
        'end_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function getImageUrlAttribute($value)
    {
        return $value ? \image_url($value) : null;
    }
}

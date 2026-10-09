<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'version_number',
        'release_notes',
        'target_audience',
        'apk_file_url',
        'play_store_url',
        'scheduled_at',
        'is_force_update',
        'is_sent',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'is_force_update' => 'boolean',
        'is_sent' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getDownloadUrlAttribute()
    {
        if ($this->apk_file_url) {
            return str_starts_with($this->apk_file_url, 'http')
                ? $this->apk_file_url
                : asset($this->apk_file_url);
        }
        return url('/download-app');
    }
}

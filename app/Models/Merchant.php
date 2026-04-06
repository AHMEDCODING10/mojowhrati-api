<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = [
        'user_id', 'store_name', 'contact_number', 'whatsapp_number', 'instagram_handle',
        'address', 'lat', 'lng', 'documents', 'logo', 'banner', 'store_description',
        'commercial_register', 'tax_number', 'approved', 'approval_notes',
        'approved_by', 'approved_at', 'store_status'
    ];

    protected $casts = [
        'documents' => 'array',
        'approved' => 'boolean',
        'approved_at' => 'datetime',
    ];

    protected $appends = ['logo_url', 'banner_url', 'whatsapp_number', 'contact_number'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'merchant_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'merchant_id');
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? \image_url($this->logo) : null;
    }

    public function getBannerUrlAttribute()
    {
        return $this->banner ? \image_url($this->banner) : null;
    }

    public function getWhatsappNumberAttribute($value)
    {
        if ($value) return $value;

        if ($this->relationLoaded('user')) {
            return $this->user->phone ?? '';
        }

        return \DB::table('users')
            ->where('id', $this->user_id)
            ->value('phone') ?? '';
    }

    public function getContactNumberAttribute($value)
    {
        if ($value) return $value;

        if ($this->relationLoaded('user')) {
            return $this->user->phone ?? '';
        }

        return \DB::table('users')
            ->where('id', $this->user_id)
            ->value('phone') ?? '';
    }
}


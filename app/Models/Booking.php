<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'product_id', 'quantity', 'customer_id', 'merchant_id', 'status', 'expires_at',
        'contact_visible', 'customer_notes', 'merchant_notes', 'total_price', 'paid_amount',
        'rejection_reason', 'rejected_at', 'confirmed_at', 'completed_at',
    ];

    protected $casts = [
        'expires_at'   => 'datetime',
        'confirmed_at' => 'datetime',
        'rejected_at'  => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'booking_id');
    }
}


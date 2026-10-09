<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionRequest extends Model
{
    protected $fillable = [
        'merchant_id',
        'type',
        'category_id',
        'product_ids',
        'duration_weeks',
        'fee_amount',
        'payment_receipt_url',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'product_ids' => 'array',
        'fee_amount' => 'decimal:2',
        'duration_weeks' => 'integer',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getReceiptUrlAttribute()
    {
        return $this->payment_receipt_url ? \image_url($this->payment_receipt_url) : null;
    }
}

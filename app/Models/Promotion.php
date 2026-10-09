<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'merchant_id',
        'type',
        'target_id',
        'title',
        'placement',
        'start_at',
        'end_at',
        'priority',
        'fee_amount',
        'payment_status',
        'status',
        'impressions_count',
        'clicks_count',
        'notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'fee_amount' => 'decimal:2',
        'priority' => 'integer',
        'impressions_count' => 'integer',
        'clicks_count' => 'integer',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'target_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'target_id');
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('status', 'active')
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
            });
    }

    public function getTargetNameAttribute()
    {
        if ($this->type === 'category' && $this->category) {
            return $this->category->name;
        }
        if ($this->type === 'product' && $this->product) {
            return $this->product->title;
        }
        if ($this->type === 'merchant_store' && $this->merchant) {
            return $this->merchant->store_name;
        }
        return $this->title ?? 'عام';
    }

    public function getIsExpiredAttribute()
    {
        return $this->end_at && $this->end_at->isPast();
    }
}

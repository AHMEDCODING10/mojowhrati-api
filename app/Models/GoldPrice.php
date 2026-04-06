<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoldPrice extends Model
{
    protected $fillable = [
        'purity',
        'price_per_gram_usd',
        'currency_code',
        'source',
        'last_updated',
        'is_active',
    ];

    public function scopeLatestByPurity($query, $purity)
    {
        return $query->where('purity', $purity)->latest()->first();
    }
}

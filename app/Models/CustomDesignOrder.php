<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomDesignOrder extends Model
{
    protected $fillable = [
        'user_id', 
        'merchant_id',
        'description', 
        'image_path', 
        'budget_range', 
        'material_preference', 
        'purity',
        'weight',
        'budget',
        'status'
    ];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return $this->image_path ? \image_url($this->image_path) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'merchant_id', 'category_id', 'material_id', 'title', 'slug', 'description', 
        'weight', 'stone_weight', 'purity', 'type', 'quantity', 'service_fee', 'manufacturer', 
        'status', 'is_featured', 'stock_quantity', 'manage_stock', 'review_notes', 
        'material_type', 'stone_type', 'clarity', 'cut'
    ];

    protected $casts = [
        'specifications' => 'array',
        'is_featured' => 'boolean',
        'manage_stock' => 'boolean',
        'weight' => 'double',
        'stone_weight' => 'double',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('display_order');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }


    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable')->where('status', 'approved');
    }

    public function getAverageRatingAttribute()
    {
        try {
            return round($this->reviews()->avg('rating') ?? 5.0, 1);
        } catch (\Exception $e) {
            return 5.0; // Fallback to 5.0 if the table or query fails
        }
    }

    public function getImageUrlAttribute()
    {
        return $this->primaryImage ? $this->primaryImage->url : ($this->images()->first()?->url ?? null);
    }

    public function getReviewsCountAttribute()
    {
        // Use loaded count if withCount() was used, otherwise query
        if (isset($this->attributes['reviews_count'])) {
            return (int) $this->attributes['reviews_count'];
        }
        try {
            return $this->reviews()->count();
        } catch (\Exception $e) {
            return 0; // Fallback to 0 if the table or query fails
        }
    }

    protected $appends = ['average_rating', 'reviews_count', 'image_url'];
}


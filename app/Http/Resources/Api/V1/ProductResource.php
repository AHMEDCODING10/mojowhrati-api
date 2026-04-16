<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'weight' => (float) $this->weight,
            'material_type' => $this->material_type,
            'purity' => $this->purity,
            'stone_type' => $this->stone_type,
            'stone_weight' => (float) $this->stone_weight,
            'clarity' => $this->clarity,
            'cut' => $this->cut,
            'type' => $this->type,
            'status' => $this->status,
            'is_featured' => (bool) $this->is_featured,
            'stock_quantity' => (int) $this->stock_quantity,
            'available_stock' => (int) $this->available_stock,
            'manage_stock' => (bool) $this->manage_stock,
            'final_price' => (float) $this->final_price,
            'workmanship' => (float) $this->service_fee,
            'created_at' => $this->created_at->toDateTimeString(),
            
            // Relationships
            'merchant' => new MerchantResource($this->whenLoaded('merchant')),
            'category' => $this->whenLoaded('category'),
            'material' => $this->whenLoaded('material'),
            'images' => $this->images->map(function($image) {
                return [
                    'id' => $image->id,
                    'url' => \image_url($image->image_url),
                    'is_primary' => (bool) $image->is_primary,
                ];
            }),
            'primary_image' => $this->primaryImage ? \image_url($this->primaryImage->image_url) : null,
        ];
    }
}

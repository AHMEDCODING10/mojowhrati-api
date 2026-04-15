<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomDesignResource extends JsonResource
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
            'description' => $this->description,
            'budget' => $this->budget,
            'purity' => $this->purity,
            'weight' => $this->weight,
            'status' => $this->status,
            'image_url' => $this->image_path ? image_url($this->image_path) : null,
            'merchant' => [
                'id' => $this->merchant->id,
                'store_name' => $this->merchant->store_name,
                'logo_url' => $this->merchant->logo_url,
            ],
            'created_at' => $this->created_at,
        ];
    }
}

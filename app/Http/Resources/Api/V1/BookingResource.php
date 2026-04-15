<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'status' => $this->status,
            'rejection_reason' => $this->rejection_reason,
            'customer_notes' => $this->customer_notes,
            'expires_at' => $this->expires_at,
            'confirmed_at' => $this->confirmed_at,
            'rejected_at' => $this->rejected_at,
            'completed_at' => $this->completed_at,
            'product' => new ProductResource($this->whenLoaded('product')),
            'merchant' => [
                'id' => $this->merchant->id,
                'store_name' => $this->merchant->store_name,
                'logo_url' => $this->merchant->logo_url,
                'contact_number' => $this->when($this->status === 'confirmed', $this->merchant->contact_number),
                'whatsapp_number' => $this->when($this->status === 'confirmed', $this->merchant->whatsapp_number),
            ],
            'customer' => [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'phone' => $this->customer->phone,
            ],
            'created_at' => $this->created_at,
        ];
    }
}

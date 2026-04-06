<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MerchantResource extends JsonResource
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
            'name' => $this->user->name ?? $this->store_name,
            'store_name' => $this->store_name,
            'store_logo' => $this->store_logo ? \image_url($this->store_logo) : null,
            'store_banner' => $this->store_banner ? \image_url($this->store_banner) : null,
            'store_description' => $this->store_description,
            'whatsapp_number' => $this->whatsapp_number ?? $this->user->phone ?? '',
            'contact_number' => $this->contact_number ?? $this->user->phone ?? '',
            'store_status' => $this->store_status,
            'approved' => (bool) $this->approved,
            'location' => $this->location,
            'instagram_url' => $this->instagram_url,
            // Sensitive fields are HIDDEN by default here, only exposed if needed
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'is_active' => $this->is_active,
            'is_featured' => $this->is_featured,
            'province_id' => $this->province_id,
            'province_name' => $this->province?->name,
            'district_id' => $this->district_id,
            'district_name' => $this->district?->name,
            'sub_district_id' => $this->sub_district_id,
            'sub_district_name' => $this->subDistrict?->name,
            'village_id' => $this->village_id,
            'village_name' => $this->village?->name,
        ];
    }
}

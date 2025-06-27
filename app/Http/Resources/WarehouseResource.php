<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'description' => $this->description,
            'provice' => $this->subDistrict->district->province->name,
            'district' => $this->subDistrict->district->name,
            'sub_district' => $this->subDistrict->name,
        ];
    }
}

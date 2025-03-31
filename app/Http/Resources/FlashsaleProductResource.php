<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FlashsaleProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'discount_percentage' => $this->discount_percentage,
            'stock' => $this->stock,
            'product' => ProductSimpleResource::make($this->product),
        ];
    }
}

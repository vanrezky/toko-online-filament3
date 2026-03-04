<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'product_id' => $this->product->uuid,
            'product' => ProductSimpleResource::make($this->product),
            'product_variant' => $this->productVariant ? [
                'id' => $this->productVariant->uuid,
                'variant_name' => $this->productVariant->variant_name,
                'sku' => $this->productVariant->sku,
            ] : null,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'discount' => $this->discount,
            'total' => ($this->price - $this->discount) * $this->quantity,
            'description' => $this->description,
        ];
    }
}

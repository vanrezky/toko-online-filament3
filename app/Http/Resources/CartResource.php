<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'items' => $this->items->map(function ($item) {
                return [
                    'id' => $item->uuid,
                    'product_id' => $item->product->uuid,
                    'product' => ProductSimpleResource::make($item->product),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'product_variant' => !empty($item->productVariant) ?
                        [
                            'id' => $item->productVariant->uuid,
                            'variant_name' => $item->productVariant->variant_name,
                            'sku' => $item->productVariant->sku,
                            'attributes' => $item->productVariant->variantAttributes->map(function ($attr) {
                                return [
                                    'name' => $attr->productAttribute?->name,
                                    'option' => $attr->productAttributeOption?->name,
                                ];
                            }),
                        ]
                        : null,
                ];
            }),
        ];
    }
}

<?php

namespace App\Http\Resources;

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
        $discountPercentage = null;
        if ($this->sale_price && $this->price > 0) {
            $discountPercentage = round((($this->price - $this->sale_price) / $this->price) * 100);
        }

        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'digital' => $this->digital,
            'description' => $this->description,
            'code' => $this->code,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'sale_price' => $this->sale_price ? money($this->sale_price)->format() : null,
            'price' => money($this->price)->format(),
            'discount_percentage' => $discountPercentage,
            'min_order' => $this->min_order,
            'thumbnail' => $this->getFirstMediaUrl(),
            'category' => CategoryResource::make($this->category),
            'images' => $this->resource->getMedia()->map(function ($media) {
                return $media->getUrl('thumb');
            }),
            'warehouse' => WarehouseResource::make($this->warehouse),
            'variants' => $this->productVariants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'variant_name' => $variant->attributes->map(fn($attr) => $attr->productAttributeOption?->name)->filter()->implode(' - '),
                    'sku' => $variant->sku,
                    'price' => money($variant->price)->format(),
                    'raw_price' => $variant->price,
                    'stock' => $variant->stock,
                    'attributes' => $variant->attributes->map(function ($attr) {
                        return [
                            'name' => $attr->productAttribute?->name,
                            'option' => $attr->productAttributeOption?->name,
                        ];
                    }),
                ];
            }),
            'faqs' => ProductFaqResource::collection($this->faqs),
            'meta' => MetaResource::make($this->whenLoaded('meta')),
        ];
    }
}

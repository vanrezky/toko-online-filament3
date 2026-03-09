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
            'id' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'digital' => $this->digital,
            'description' => $this->description,
            'code' => $this->code,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'sale_price' => $this->sale_price ? money($this->sale_price)->format() : null,
            'raw_sale_price' => $this->sale_price,
            'price' => money($this->price)->format(),
            'raw_price' => $this->price,
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
                    'id' => $variant->uuid,
                    'variant_name' => $variant->variant_name,
                    'sku' => $variant->sku,
                    'price' => money($variant->price)->format(),
                    'raw_price' => $variant->price,
                    'stock' => $variant->stock,
                    'attributes' => $variant->variantAttributes->map(function ($attr) {
                        return [
                            'name' => $attr->productAttribute?->name,
                            'option' => $attr->productAttributeOption?->name,
                        ];
                    }),
                ];
            }),
            'wholesales' => $this->wholesales->map(function ($wholesale) {
                return [
                    'min_qty' => $wholesale->min_qty,
                    'price' => money($wholesale->price)->format(),
                    'raw_price' => $wholesale->price,
                ];
            }),
            'faqs' => ProductFaqResource::collection($this->faqs),
            'meta' => MetaResource::make($this->whenLoaded('meta')),
        ];
    }
}

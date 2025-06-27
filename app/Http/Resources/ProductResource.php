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
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'digital' => $this->digital,
            'description' => $this->description,
            'code' => $this->code,
            'stock' => $this->stock,
            'sale_price' => $this->sale_price ? money($this->sale_price)->format() : null,
            'price' => money($this->price)->format(),
            'min_order' => $this->min_order,
            'thumbnail' => $this->resource->getMedia()[0]->getUrl('thumb'),
            'category' => CategoryResource::make($this->category),
            'images' => $this->resource->getMedia()->map(function ($media) {
                return $media->getUrl('thumb');
            }),
            'warehouse' => WarehouseResource::make($this->warehouse),
            'variants' => $this->productVariants,
            'faqs' => ProductFaqResource::collection($this->faqs),
        ];
    }

    public function mapProductVariant($productVariant)
    {
        return $productVariant->map(function ($variant) {
            return [
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock' => $variant->stock,
                'dimensions' => $variant->dimensions,
                'status' => $variant->status,
                'variant_name' => $variant->product_variant->name,
                // 'variant_'
            ];
        });
    }
}

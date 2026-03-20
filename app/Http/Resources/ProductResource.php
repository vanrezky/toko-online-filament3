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
        $customer = auth('customer')->user();
        $resellerId = $customer?->reseller_id;

        $targetPrice = $this->price;
        if ($resellerId) {
            $resellerPrice = $this->resellerPrices->where('reseller_id', $resellerId)->first();
            if ($resellerPrice) {
                $targetPrice = $resellerPrice->price;
            }
        }

        $discountPercentage = null;
        if ($this->sale_price && $targetPrice > 0) {
            $discountPercentage = round((($this->sale_price - $targetPrice) / $this->sale_price) * 100);
        }

        $wholesales = $this->wholesales->where('reseller_id', $resellerId);

        return [
            'id' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'digital' => $this->digital,
            'description' => $this->description,
            'code' => $this->code,
            'stock' => $this->stock,
            'weight' => $this->weight,
            'sale_price' => $this->sale_price,
            'raw_sale_price' => $this->sale_price,
            'price' => $targetPrice,
            'discount_percentage' => $discountPercentage,
            'min_order' => $this->min_order,
            'thumbnail' => $this->getFirstMediaUrl(),
            'category' => CategoryResource::make($this->category),
            'images' => $this->resource->getMedia()->map(function ($media) {
                return $media->getUrl('thumb');
            }),
            'warehouse' => WarehouseResource::make($this->warehouse),
            'variants' => $this->productVariants->map(function ($variant) use ($targetPrice) {
                return [
                    'id' => $variant->uuid,
                    'variant_name' => $variant->variant_name,
                    'sku' => $variant->sku,
                    'price' => $variant->price,
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
            'wholesales' => $wholesales->count() > 0
                ? $wholesales->map(fn($w) => [
                    'min_qty' => $w->min_qty,
                    'price' => $w->price,
                    'raw_price' => $w->price,
                ])->prepend([
                    'min_qty' => (int) ($this->min_order ?: 1),
                    'price' => $targetPrice,
                    'raw_price' => $targetPrice,
                ])->unique('min_qty')->sortBy('min_qty')->values()
                : [],
            'faqs' => ProductFaqResource::collection($this->faqs),
            'meta' => MetaResource::make($this->whenLoaded('meta')),
        ];
    }
}

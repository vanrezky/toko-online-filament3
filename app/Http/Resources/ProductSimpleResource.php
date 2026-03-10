<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProductSimpleResource extends JsonResource
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

        return [
            'id' => $this->uuid,
            'name' => Str::limit($this->name, 35, ''),
            'slug' => $this->slug,
            'category_name' => $this->category?->name,
            'digital' => $this->digital,
            'description' => $this->description,
            'code' => $this->code,
            'stock' => $this->stock,
            'sale_price' => $this->sale_price ? money($this->sale_price)->format() : null,
            'price' => money($targetPrice)->format(),
            'discount_percentage' => $discountPercentage,
            'min_order' => $this->min_order,
            'thumbnail' => $this->resource->getMedia()->first()?->getUrl('thumb'),
            'currency' => currency()
        ];
    }
}

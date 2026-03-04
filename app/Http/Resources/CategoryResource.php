<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            // 'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->getFirstMediaUrl(),
        ];

        if (isset($this->products_count)) {
            $data['products_count'] = $this->products_count;
        }

        if ($this->relationLoaded('products')) {
            $data['products'] = ProductSimpleResource::collection($this->products);
        }

        return $data;
    }
}

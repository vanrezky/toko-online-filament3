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
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->getFirstMediaUrl(),
        ];

        $this->when($this->products_count, fn() => $data['products_count'] = $this->products_count);

        return $data;

        // return parent::toArray($request);
    }
}

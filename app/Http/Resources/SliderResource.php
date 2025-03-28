<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SliderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'description' => $this->description,
            'target_link' => $this->target_link,
            'target_anchor' => $this->target_anchor,
            'description' => $this->description,
            'image_url' => $this->getFirstMediaUrl(),
        ];
    }
}

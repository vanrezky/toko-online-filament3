<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'position' => $this->position,
            'target_link' => $this->target_link,
            'target_anchor' => $this->target_anchor,
            'image_url' => $this->getFirstMediaUrl(),
        ];
    }
}

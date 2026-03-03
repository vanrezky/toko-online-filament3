<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BlogPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => Str::limit(strip_tags($this->content), 150),
            'published_at' => $this->published_at ? $this->published_at->format('M d, Y') : null,
            'image_url' => $this->image ? asset('storage/' . $this->image) : null,
            'category' => [
                'name' => $this->category?->name,
                'slug' => $this->category?->slug,
            ],
            'author' => [
                'name' => $this->author?->name,
            ],
            'tags' => $this->tags->pluck('name'),
            'meta' => MetaResource::make($this->whenLoaded('meta')),
        ];
    }
}

<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Promotion extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasUuidTrait;

    protected $fillable = ['title', 'description', 'is_active', 'start_at', 'end_at', 'position', 'target_link', 'target_anchor'];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('start_at')
                    ->orWhere('start_at', '<=', now());
            })
            ->where(function ($query) {
                $query->whereNull('end_at')
                    ->orWhere('end_at', '>=', now());
            });
    }

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(3)
            ->sharpen(10)
            ->nonQueued();
    }
}

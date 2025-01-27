<?php

namespace App\Models;

use App\Traits\HasMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory, HasMeta, InteractsWithMedia;

    protected $fillable = ['name', 'slug', 'image', 'is_active', 'is_featured'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function getImageUrlAttribute(): string
    {
        $media = $this->getFirstMediaUrl() ?? asset('no-image');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeHomepage($query)
    {
        return $query->active()->featured()->whereNotNull('image');
    }

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('preview')
            ->fit(Fit::Contain, 300, 300)
            ->nonQueued();
    }
}

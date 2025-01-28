<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['product_id', 'variant_name', 'sku', 'price', 'stock', 'weight', 'dimensions', 'status'];

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Fit::Contain, 300, 300)
            ->sharpen(10)
            ->nonQueued();
    }
}

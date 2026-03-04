<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ProductVariant extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasUuidTrait;

    protected $fillable = ['uuid', 'product_id', 'sku', 'price', 'stock', 'weight', 'dimensions', 'status'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    protected $appends = ['variant_name'];

    public function variantName(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('variantAttributes')) {
                    return $this->variantAttributes->map(function ($attr) {
                        return $attr->productAttributeOption?->name;
                    })->filter()->implode(' - ');
                }
                return '';
            }
        );
    }

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    public function variantAttributes(): HasMany
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

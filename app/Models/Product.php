<?php

namespace App\Models;

use App\Constants\UploadPath;
use App\Enums\ProductActiveStatus;
use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = ['name', 'slug', 'warehouse_id', 'category_id', 'digital', 'digital_url', 'description', 'code', 'images', 'weight', 'stock', 'price', 'sale_price', 'afiliate_price', 'min_order', 'variant', 'sub_variant', 'user_id', 'security_stock'];

    protected $casts = [
        'images' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function priceCurrency(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) =>  toMoney($attributes['price']),
        );
    }
    protected function salePriceCurrency(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => toMoney($attributes['sale_price']),
        );
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                if (!empty($attributes['images'])) {

                    $images = json_decode($attributes['images'], true);
                    return getUrlImage($images[0]);
                }

                return null;
            },
        );
    }

    protected function savePriceCurrency(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value,  array $attributes) {
                if (!empty($attributes['sale_price']) && $attributes['sale_price'] > $attributes['price']) {
                    $savePrice = $attributes['sale_price'] - $attributes['price'];
                    return toMoney($savePrice);
                }


                return null;
            },
        );
    }

    protected function discountPercentace(): Attribute
    {
        return Attribute::make(get: fn (mixed $value, array $attributes) => round(($attributes['sale_price'] - $attributes['price']) / $attributes['price'] * 100));
    }



    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resellerPrices(): HasMany
    {
        return $this->hasMany(ProductResellerPrice::class);
    }

    public function wholesales(): HasMany
    {
        return $this->hasMany(ProductWholesale::class);
    }
}

<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
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
        'images' => 'array'
    ];

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
}

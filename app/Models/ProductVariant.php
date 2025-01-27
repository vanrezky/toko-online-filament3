<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'variant_name', 'sku', 'price', 'stock', 'weight', 'dimensions', 'status', 'image'];

    public function attributes(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }
}

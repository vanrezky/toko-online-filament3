<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['product_variant_id', 'product_attribute_id', 'product_attribute_option_id'];

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function productAttribute()
    {
        return $this->belongsTo(ProductAttribute::class);
    }

    public function productAttributeOption()
    {
        return $this->belongsTo(ProductAttributeOption::class);
    }
}

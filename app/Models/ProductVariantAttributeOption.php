<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariantAttributeOption extends Model
{
    use HasFactory;
    protected $fillable = ['product_variant_attribute_id', 'name'];

    public function attribute(): BelongsTo
    {
        return $this->belongsTo(ProductVariantAttribute::class, 'attribute_id');
    }
}

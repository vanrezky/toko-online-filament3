<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariantAttribute extends Model
{
    use HasFactory;
    protected $fillable = ['product_variant_id', 'attribute_name', 'attribute_value'];
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductVariantAttribute extends Model
{
    use HasFactory;
    protected $fillable = ['product_variant_id', 'name'];
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function attributeOption(): HasMany
    {
        return $this->hasMany(ProductVariantAttributeOption::class, 'attribute_id');
    }
}

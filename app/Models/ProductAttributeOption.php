<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttributeOption extends Model
{
    use HasFactory;

    protected $fillable = ['product_attribute_id', 'name', 'user_id', 'product_id', 'status', 'is_global'];

    protected $casts  = [
        'is_global' => 'boolean',
    ];

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function productVariantAttributeOption(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }
}

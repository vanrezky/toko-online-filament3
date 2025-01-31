<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'user_id', 'product_id', 'status', 'is_global'];

    protected $casts  = [
        'is_global' => 'boolean',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductAttributeOption::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}

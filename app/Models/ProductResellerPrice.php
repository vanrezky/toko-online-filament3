<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductResellerPrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'reseller_id', 'product_id', 'price'
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class);
    }

    public function reseller(): HasOne
    {
        return $this->hasOne(Reseller::class);
    }

    public function wholesales(): HasMany
    {
        return $this->hasMany(ProductWholesale::class, 'reseller_id');
    }
}

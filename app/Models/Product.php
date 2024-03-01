<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'warehouse_id', 'category_id', 'digital', 'digital_url', 'description', 'code', 'images', 'weight', 'stock', 'price', 'sale_price', 'afiliate_price', 'min_order', 'variation', 'sub_variation',];

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
}

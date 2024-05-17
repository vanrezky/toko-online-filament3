<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductWholesale extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'min_qty', 'max_qty', 'price'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function reseller()
    {
        return $this->belongsTo(Reseller::class);
    }
}

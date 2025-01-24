<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductFlashsale extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'flashsale_id', 'start_time', 'end_time', 'discount_percentage', 'stock'];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function flashsale(): BelongsTo
    {
        return $this->belongsTo(Flashsale::class);
    }
}

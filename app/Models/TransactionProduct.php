<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionProduct extends Model
{
    use HasFactory, HasUuidTrait;

    protected $table = 'transcation_products';

    protected $fillable = [
        'uuid',
        'transaction_id',
        'customer_id',
        'is_digital',
        'product_id',
        'warehouse_id',
        'quantity',
        'price',
        'discount',
        'description',
    ];
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    public function getSubtotalAttribute(): float
    {
        return ($this->price * $this->quantity) - $this->discount;
    }
}

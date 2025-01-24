<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartVoucher extends Model
{
    use HasFactory;
    protected $fillable = ['cart_id', 'voucher_code', 'discount_amount'];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }
}

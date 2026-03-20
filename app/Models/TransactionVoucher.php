<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionVoucher extends Model
{
    protected $fillable = [
        'transaction_id',
        'voucher_code',
        'voucher_name',
        'voucher_type',
        'discount_type',
        'discount_value',
        'discount_amount',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function getFormattedDiscountAttribute(): string
    {
        if ($this->discount_type === 'percentage') {
            return $this->discount_value . '%';
        }

        return 'Rp ' . number_format($this->discount_value, 0, ',', '.');
    }

    public function isShipping(): bool
    {
        return $this->voucher_type === 'shipping';
    }

    public function isProduct(): bool
    {
        return $this->voucher_type === 'product';
    }
}

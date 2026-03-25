<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'customer_id',
        'customer_address_id',
        'weight',
        'shipping_cost',
        'courier_id',
        'from_district_id',
        'to_district_id',
        'cod',
        'cod_fee',
        'payment_method',
        'status',
        'notes',
        'uuid',
        'timelimit',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(TransactionProduct::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class, 'customer_address_id');
    }

    public function shippingDetails(): HasMany
    {
        return $this->hasMany(TransactionShippingDetail::class);
    }

    public function vouchers(): HasMany
    {
        return $this->hasMany(TransactionVoucher::class);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->products->sum(fn($p) => $p->price * $p->quantity);
    }

    public function getProductDiscountAttribute(): float
    {
        return (float) $this->products->sum('discount');
    }

    public function getVoucherDiscountAttribute(): float
    {
        return (float) $this->vouchers->sum('discount_amount');
    }

    public function getTotalDiscountAttribute(): float
    {
        return $this->product_discount + $this->voucher_discount;
    }

    public function getTotalAmountAttribute(): float
    {
        return $this->subtotal - $this->total_discount + $this->shipping_cost + ($this->cod ? $this->cod_fee : 0);
    }

    public function getTotalItemsAttribute(): int
    {
        return (int) $this->products->sum('quantity');
    }

    public function getDigitalProductsCountAttribute(): int
    {
        return (int) $this->products->where('is_digital', true)->count();
    }

    public function hasDigitalProducts(): bool
    {
        return $this->digital_products_count > 0;
    }
}

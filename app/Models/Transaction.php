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
}

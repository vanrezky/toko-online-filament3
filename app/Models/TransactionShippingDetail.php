<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionShippingDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'warehouse_id',
        'courier_code',
        'courier_name',
        'price',
        'weight',
        'estimation',
    ];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}

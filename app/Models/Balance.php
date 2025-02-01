<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'amount', 'charge', 'post_balance', 'trx_type', 'notes', 'remark'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function updateBalance(int $customer_id, float|int $amount, float|int $charge, float|int $post_balance, string $trx_type = '+', ?string $notes = null, ?string $remark = null)
    {
        $defaultNotes = $trx_type === '+' ? 'Add Balance' : 'Reduce Balance';
        $defaultRemark = $trx_type === '+' ? 'Add Balance' : 'Reduce Balance';

        return [
            'customer_id' => $customer_id,
            'amount' => $amount,
            'charge' => $charge,
            'post_balance' => $post_balance,
            'trx_type' => $trx_type,
            'notes' => $notes ?: $defaultNotes,
            'remark' => $remark ?: $defaultRemark
        ];
    }
}

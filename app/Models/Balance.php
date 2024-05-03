<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Balance extends Model
{
    use HasFactory;

    protected $fillable = ['customer_id', 'amount', 'charge', 'post_balance', 'trx_type', 'notes', 'remark'];


    public function addBalance(int $customer_id, float|int $amount, float|int $charge, float|int $post_balance, string $trx_type = '+', ?string $notes = null, ?string $remark = null)
    {
        return [
            'customer_id' => $customer_id,
            'amount' => $amount,
            'charge' => $charge,
            'post_balance' => $post_balance,
            'trx_type' => $trx_type,
            'notes' => $notes ?  $notes : 'Add Balance',
            'remark' => $remark ? $remark : 'Add Balance'
        ];
    }

    public function reduceBalance(int $customer_id, float|int $amount, float|int $charge, float|int $post_balance, string $trx_type = '-', ?string $notes = null, ?string $remark = null)
    {
        return [
            'customer_id' => $customer_id,
            'amount' => $amount,
            'charge' => $charge,
            'post_balance' => $post_balance,
            'trx_type' => $trx_type,
            'notes' => $notes ?  $notes : 'Reduce Balance',
            'remark' => $remark ? $remark : 'Reduce Balance'
        ];
    }
}

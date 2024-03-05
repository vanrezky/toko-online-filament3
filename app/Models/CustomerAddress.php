<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerAddress extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'is_active', 'sub_district_id', 'name', 'phone', 'address', 'postal_code'];


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function subDistrict(): BelongsTo
    {
        return $this->belongsTo(SubDistrict::class);
    }
}

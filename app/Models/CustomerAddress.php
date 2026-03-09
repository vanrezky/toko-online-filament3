<?php

namespace App\Models;

use App\Traits\HasModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerAddress extends Model
{
    use HasFactory, HasModelTrait, SoftDeletes;
    protected $fillable = ['customer_id', 'is_active', 'is_featured', 'province_id', 'district_id', 'sub_district_id', 'village_id', 'name', 'phone', 'address', 'postal_code'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function subDistrict(): BelongsTo
    {
        return $this->belongsTo(SubDistrict::class);
    }

    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }
}

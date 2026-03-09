<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Scope;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['sub_district_id', 'province_id', 'district_id', 'village_id', 'postal_code', 'name', 'address', 'contact_name', 'contact_phone', 'courier', 'description', 'is_active'];

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

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}

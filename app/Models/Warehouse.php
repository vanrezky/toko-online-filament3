<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['sub_district_id', 'name', 'address', 'contact_name', 'contact_phone', 'courier', 'description', 'is_active'];

    public function subDistrict(): BelongsTo
    {
        return $this->belongsTo(SubDistrict::class);
    }
}

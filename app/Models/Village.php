<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Village extends Model
{
    use HasFactory;
    protected $fillable = [
        'sub_district_id',
        'name',
        'postal_code',
        'rajaongkir',
        'apicoid_code',
    ];

    public function subDistrict(): BelongsTo
    {
        return $this->belongsTo(SubDistrict::class);
    }
}

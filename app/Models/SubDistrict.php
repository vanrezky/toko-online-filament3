<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubDistrict extends Model
{
    use HasFactory;

    protected $fillable = ['district_id', 'name', 'postal_code', 'rajaongkir', 'postal_code'];

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}

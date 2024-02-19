<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubDistrict extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'postal_code', 'rajaongkir'];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}

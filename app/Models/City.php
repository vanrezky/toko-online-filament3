<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['province_id', 'name', 'type', 'rajaongkir'];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function subDistricts(): HasMany
    {
        return $this->hasMany(SubDistrict::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}

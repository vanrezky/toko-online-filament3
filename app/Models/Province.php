<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Province extends Model
{
    use HasFactory;

    protected $fillable  = ['country_id', 'name', 'rajaongkir'];


    public function setNameAttribute($name)
    {
        return $this->attributes['name'] = Str::upper($name);
    }


    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function district(): HasMany
    {
        return $this->hasMany(District::class);
    }
}

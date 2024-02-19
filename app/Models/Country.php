<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['iso', 'name'];

    public function province(): HasMany
    {
        return $this->hasMany(Province::class);
    }

    public function getNameAttribute($name): String
    {
        return Str::upper($name);
    }
}

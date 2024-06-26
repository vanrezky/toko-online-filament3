<?php

namespace App\Models;

use App\Traits\HasModelTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reseller extends Model
{
    use HasFactory, HasModelTrait;

    protected $fillable = ['name', 'description', 'is_active', 'level'];


    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function getNameLevelAttribute()
    {
        return "{$this->name} (level $this->level)";
    }
}

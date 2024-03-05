<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DistributorLevel extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'is_active', 'level'];


    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flashsale extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_time', 'end_time'];

    public function products(): HasMany
    {
        return $this->hasMany(ProductFlashsale::class);
    }
}

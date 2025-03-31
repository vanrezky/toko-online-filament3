<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flashsale extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_time', 'end_time'];

    public function scopeActive($condition)
    {
        $now = now()->format('Y-m-d H:i:s');
        $condition->whereRaw("'{$now}' between start_time and end_time");
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductFlashsale::class);
    }
}

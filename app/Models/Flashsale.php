<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Flashsale extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'start_time', 'end_time', 'is_active'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($flashsale) {
            if ($flashsale->is_active) {
                static::where('id', '!=', $flashsale->id)->update(['is_active' => false]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now());
    }

    public function products(): HasMany
    {
        return $this->hasMany(ProductFlashsale::class);
    }
}

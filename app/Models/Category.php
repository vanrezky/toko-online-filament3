<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'is_active', 'is_featured'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}

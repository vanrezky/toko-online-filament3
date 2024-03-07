<?php

namespace App\Traits;

trait HasModelTrait
{
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}

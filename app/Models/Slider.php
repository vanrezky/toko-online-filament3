<?php

namespace App\Models;

use App\Constants\UploadPath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = ['description', 'image', 'is_active', 'target_link', 'target_anchor', 'start_at', 'end_at'];

    public function getImageUrlAttribute(): string
    {
        return $this->image ? getUrlImage($this->image) : '';
    }


    public function scopeActive($condition)
    {
        return $condition->where('is_active', true);
    }
}

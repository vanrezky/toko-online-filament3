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

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value ? asset('/storage/' .  $value) : null
        );
    }

    public function scopeActive($condition)
    {
        return $condition->where('is_active', true);
    }
}

<?php

namespace App\Models;

use App\Constants\UploadPath;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = ['description', 'is_active', 'target_link', 'target_anchor', 'start_at', 'end_at'];

    public function getImageUrlAttribute(): string
    {
        return $this->image ? getUrlImage($this->image) : '';
    }


    public function scopeActive($condition)
    {
        return $condition->where('is_active', true);
    }

    public function registerAllMediaConversions(?Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->width(200)
            ->sharpen(10)
            ->nonQueued();
    }
}

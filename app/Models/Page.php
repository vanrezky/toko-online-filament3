<?php

namespace App\Models;

use App\Enums\BlogPostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Page extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'image', 'is_status', 'slug', 'order', 'published_at', 'parent_id'];
    protected $casts = [
        'is_status' => BlogPostStatus::class
    ];


    public function scopeActive($query)
    {
        return $query->where('is_status', true);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
}

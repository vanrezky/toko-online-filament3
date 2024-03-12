<?php

namespace App\Models;

use App\Enums\BlogPostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'content', 'published_at', 'blog_category_id', 'image', 'is_status', 'user_id'];

    protected $casts = [
        'is_status' => BlogPostStatus::class
    ];


    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function tags(): BelongsTo
    {
        return $this->belongsTo(BlogTag::class);
    }

    public function author(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }
}

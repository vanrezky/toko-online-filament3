<?php

namespace App\Models;

use App\Enums\BlogPostStatus;
use App\Traits\HasMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Page extends Model
{
    use HasFactory, HasMeta;

    protected $fillable = ['title', 'content', 'image', 'is_status', 'slug', 'order', 'published_at', 'parent_id', 'show_in_menu', 'menu_location'];
    protected $casts = [
        'is_status' => BlogPostStatus::class,
        'show_in_menu' => 'boolean',
    ];


    public function scopeActive($query)
    {
        return $query->where('is_status', BlogPostStatus::PUBLISHED);
    }

    public function scopeHeaderMenu($query)
    {
        return $query->active()
            ->where('show_in_menu', true)
            ->whereIn('menu_location', ['header', 'both'])
            ->orderBy('order')
            ->orderBy('created_at');
    }

    public function scopeFooterMenu($query)
    {
        return $query->active()
            ->where('show_in_menu', true)
            ->whereIn('menu_location', ['footer', 'both'])
            ->orderBy('order')
            ->orderBy('created_at');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }
}

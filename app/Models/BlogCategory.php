<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'image', 'description', 'is_visible'];

    public function posts(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }
}

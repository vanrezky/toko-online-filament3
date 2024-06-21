<?php

namespace App\Models;

use App\Traits\HasMeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class BlogCategory extends Model
{
    use HasFactory, HasMeta;

    protected $fillable = ['name', 'slug', 'description', 'is_visible'];

    public function posts(): BelongsTo
    {
        return $this->belongsTo(BlogPost::class);
    }
}

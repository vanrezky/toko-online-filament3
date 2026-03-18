<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'name',
        'code',
        'description',
        'color_scheme',
        'thumbnail',
        'is_active',
    ];

    protected $casts = [
        'color_scheme' => 'array',
        'is_active'    => 'boolean',
    ];

    /**
     * Scope to get only active templates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get all sections for this template.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(TemplateSection::class)->orderBy('order_priority');
    }

    /**
     * Get only active sections for this template, ordered by priority.
     */
    public function activeSections(): HasMany
    {
        return $this->hasMany(TemplateSection::class)
            ->where('is_active', true)
            ->orderBy('order_priority');
    }
}

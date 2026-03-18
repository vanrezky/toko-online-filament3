<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateSectionContent extends Model
{
    use HasFactory, HasUuidTrait;

    protected $table = 'template_section_contents';

    protected $fillable = [
        'section_id',
        'field_id',
        'value',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the section this content belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(TemplateSection::class, 'section_id');
    }

    /**
     * Get the field definition this content is for.
     */
    public function field(): BelongsTo
    {
        return $this->belongsTo(TemplateSectionField::class, 'field_id');
    }
}

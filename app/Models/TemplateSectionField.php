<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TemplateSectionField extends Model
{
    use HasFactory, HasUuidTrait;

    protected $table = 'template_section_fields';

    protected $fillable = [
        'section_id',
        'key',
        'label',
        'type',
        'placeholder',
        'default_value',
        'options',
        'is_required',
        'order_priority',
    ];

    protected $casts = [
        'options'        => 'array',
        'is_required'    => 'boolean',
        'order_priority' => 'integer',
    ];

    /**
     * Field type constants.
     */
    const FIELD_TEXT     = 'text';
    const FIELD_TEXTAREA = 'textarea';
    const FIELD_RICHTEXT = 'richtext';
    const FIELD_IMAGE    = 'image';
    const FIELD_URL      = 'url';
    const FIELD_SELECT   = 'select';
    const FIELD_TOGGLE   = 'toggle';
    const FIELD_COLOR    = 'color';
    const FIELD_NUMBER   = 'number';

    /**
     * Get all available field types.
     */
    public static function fieldTypes(): array
    {
        return [
            self::FIELD_TEXT     => 'Text',
            self::FIELD_TEXTAREA => 'Textarea',
            self::FIELD_RICHTEXT => 'Rich Text',
            self::FIELD_IMAGE    => 'Image URL',
            self::FIELD_URL      => 'URL / Link',
            self::FIELD_SELECT   => 'Select / Dropdown',
            self::FIELD_TOGGLE   => 'Toggle / Boolean',
            self::FIELD_COLOR    => 'Color Picker',
            self::FIELD_NUMBER   => 'Number',
        ];
    }

    /**
     * Get the section this field belongs to.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(TemplateSection::class, 'section_id');
    }

    /**
     * Get the content (value) associated with this field.
     */
    public function content(): HasOne
    {
        return $this->hasOne(TemplateSectionContent::class, 'field_id');
    }
}

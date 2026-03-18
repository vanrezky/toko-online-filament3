<?php

namespace App\Models;

use App\Traits\HasUuidTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateSection extends Model
{
    use HasFactory, HasUuidTrait;

    protected $fillable = [
        'template_id',
        'name',
        'type',
        'description',
        'icon',
        'is_active',
        'order_priority',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'order_priority' => 'integer',
    ];

    /**
     * Section type constants - extend as needed.
     */
    const TYPE_HERO         = 'hero';
    const TYPE_STORIES      = 'stories';
    const TYPE_BANNER       = 'banner';
    const TYPE_GALLERY      = 'gallery';
    const TYPE_CTA          = 'cta';
    const TYPE_TESTIMONIALS = 'testimonials';
    const TYPE_PRODUCTS     = 'products';
    const TYPE_FAQ          = 'faq';
    const TYPE_CONTACT      = 'contact';
    const TYPE_CUSTOM       = 'custom';

    /**
     * Get list of available section types.
     */
    public static function types(): array
    {
        return [
            self::TYPE_HERO         => 'Hero / Header',
            self::TYPE_STORIES      => 'Stories / Highlights',
            self::TYPE_BANNER       => 'Banner',
            self::TYPE_GALLERY      => 'Gallery',
            self::TYPE_CTA          => 'Call to Action (CTA)',
            self::TYPE_TESTIMONIALS => 'Testimonials',
            self::TYPE_PRODUCTS     => 'Products',
            self::TYPE_FAQ          => 'FAQ',
            self::TYPE_CONTACT      => 'Contact',
            self::TYPE_CUSTOM       => 'Custom',
        ];
    }

    /**
     * Get the template this section belongs to.
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    /**
     * Get all field definitions for this section.
     */
    public function fields(): HasMany
    {
        return $this->hasMany(TemplateSectionField::class, 'section_id')->orderBy('order_priority');
    }

    /**
     * Get all user-inputted content values for this section.
     */
    public function contents(): HasMany
    {
        return $this->hasMany(TemplateSectionContent::class, 'section_id');
    }
}

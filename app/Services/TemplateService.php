<?php

namespace App\Services;

use App\Models\Template;
use App\Models\TemplateSection;
use App\Models\TemplateSectionContent;
use Illuminate\Support\Facades\Cache;

class TemplateService
{
    protected const CACHE_PREFIX = 'template_';
    protected const CACHE_TTL = 3600; // 1 hour

    public function getActiveTemplate(): ?Template
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'active',
            self::CACHE_TTL,
            function () {
                return Template::active()->with([
                    'sections.fields',
                    'sections.contents'
                ])->first();
            }
        );
    }

    public function getActiveTemplateWithSections(): ?array
    {
        $template = $this->getActiveTemplate();
        
        if (!$template) {
            return null;
        }

        return [
            'template' => $template,
            'sections' => $this->formatSections($template),
            'color_scheme' => $template->color_scheme,
        ];
    }

    public function getSectionContent(TemplateSection $section, string $key, mixed $default = null): mixed
    {
        $content = $section->contents
            ->filter(fn($c) => $c->field?->key === $key)
            ->first();

        return $content?->value ?? $default;
    }

    public function getSectionContents(TemplateSection $section): array
    {
        return $section->contents
            ->mapWithKeys(fn($c) => [$c->field?->key ?? 'unknown' => $c->value])
            ->toArray();
    }

    public function formatSections(Template $template): array
    {
        return $template->sections
            ->filter(fn($s) => $s->is_active)
            ->map(function ($section) {
                return [
                    'id' => $section->id,
                    'uuid' => $section->uuid,
                    'name' => $section->name,
                    'type' => $section->type,
                    'description' => $section->description,
                    'icon' => $section->icon,
                    'contents' => $this->getSectionContents($section),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getSectionByType(string $type): ?array
    {
        $sections = $this->getActiveTemplateWithSections();
        
        if (!$sections) {
            return null;
        }

        return collect($sections['sections'])
            ->first(fn($s) => $s['type'] === $type);
    }

    public function getColorScheme(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX . 'colors',
            self::CACHE_TTL,
            function () {
                $template = Template::active()->first();
                return $template?->color_scheme ?? $this->getDefaultColorScheme();
            }
        );
    }

    public function getDefaultColorScheme(): array
    {
        return [
            'primary'     => '#F97316',
            'secondary'   => '#F5F3FC',
            'accent'      => '#FB923C',
            'destructive' => '#F43F5E',
            'background'  => '#FCFCFE',
            'foreground'  => '#2D1B0E',
        ];
    }

    public function clearCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'active');
        Cache::forget(self::CACHE_PREFIX . 'colors');
    }

    public function warmCache(): void
    {
        $this->clearCache();
        $this->getActiveTemplateWithSections();
        $this->getColorScheme();
    }
}

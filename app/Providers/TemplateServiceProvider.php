<?php

namespace App\Providers;

use App\Models\Template;
use App\Models\TemplateSection;
use App\Models\TemplateSectionContent;
use App\Services\TemplateService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class TemplateServiceProvider extends ServiceProvider
{
    protected const CACHE_PREFIX = 'template_';

    public function register(): void
    {
        $this->app->singleton(TemplateService::class, function ($app) {
            return new TemplateService();
        });
    }

    public function boot(): void
    {
        $this->clearCacheOnSectionUpdate();
    }

    protected function clearCacheOnSectionUpdate(): void
    {
        Template::updated(function () {
            $this->clearTemplateCache();
        });

        TemplateSection::updated(function () {
            $this->clearTemplateCache();
        });

        TemplateSectionContent::updated(function () {
            $this->clearTemplateCache();
        });

        TemplateSectionContent::created(function () {
            $this->clearTemplateCache();
        });

        TemplateSectionContent::deleted(function () {
            $this->clearTemplateCache();
        });
    }

    protected function clearTemplateCache(): void
    {
        Cache::forget(self::CACHE_PREFIX . 'active');
        Cache::forget(self::CACHE_PREFIX . 'colors');
    }
}

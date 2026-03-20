<?php

namespace App\Console\Commands;

use App\Services\TemplateService;
use Illuminate\Console\Command;

class ClearTemplateCache extends Command
{
    protected $signature = 'template:cache {--warm : Warm the cache after clearing}';
    protected $description = 'Clear template settings cache';

    public function handle(TemplateService $templateService): int
    {
        $this->info('Clearing template cache...');
        
        $templateService->clearCache();
        
        $this->info('Template cache cleared successfully.');
        
        if ($this->option('warm')) {
            $this->info('Warming template cache...');
            $templateService->warmCache();
            $this->info('Template cache warmed successfully.');
        }
        
        return Command::SUCCESS;
    }
}

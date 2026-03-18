<?php

namespace App\Filament\Resources\TemplateSectionResource\Pages;

use App\Filament\Resources\TemplateSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTemplateSections extends ListRecords
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

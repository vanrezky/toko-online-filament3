<?php

namespace App\Filament\Resources\TemplateSectionResource\Pages;

use App\Filament\Resources\TemplateSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTemplateSection extends EditRecord
{
    protected static string $resource = TemplateSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

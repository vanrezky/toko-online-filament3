<?php

namespace App\Filament\Resources\DistributorLevelResource\Pages;

use App\Filament\Resources\DistributorLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDistributorLevel extends EditRecord
{
    protected static string $resource = DistributorLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

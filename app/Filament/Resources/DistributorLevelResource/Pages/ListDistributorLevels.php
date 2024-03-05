<?php

namespace App\Filament\Resources\DistributorLevelResource\Pages;

use App\Filament\Resources\DistributorLevelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDistributorLevels extends ListRecords
{
    protected static string $resource = DistributorLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

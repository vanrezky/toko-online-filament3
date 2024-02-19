<?php

namespace App\Filament\Resources\SubDistrictResource\Pages;

use App\Filament\Resources\SubDistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSubDistrict extends EditRecord
{
    protected static string $resource = SubDistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

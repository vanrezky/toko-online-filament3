<?php

namespace App\Filament\Resources\EmpeloyeeResource\Pages;

use App\Filament\Resources\EmpeloyeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEmpeloyee extends ViewRecord
{
    protected static string $resource = EmpeloyeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\EmpeloyeeResource\Pages;

use App\Filament\Resources\EmpeloyeeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmpeloyee extends EditRecord
{
    protected static string $resource = EmpeloyeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}

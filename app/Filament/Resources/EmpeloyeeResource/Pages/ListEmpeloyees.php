<?php

namespace App\Filament\Resources\EmpeloyeeResource\Pages;

use App\Filament\Resources\EmpeloyeeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmpeloyees extends ListRecords
{
    protected static string $resource = EmpeloyeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

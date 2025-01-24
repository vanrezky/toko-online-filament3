<?php

namespace App\Filament\Resources\FlashsaleResource\Pages;

use App\Filament\Resources\FlashsaleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlashsale extends EditRecord
{
    protected static string $resource = FlashsaleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

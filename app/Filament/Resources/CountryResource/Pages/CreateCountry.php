<?php

namespace App\Filament\Resources\CountryResource\Pages;

use App\Filament\Resources\CountryResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Country Created')
            ->body('The Country created successfully')
            ->success();
    }
}

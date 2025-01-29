<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\ProductVariant;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filement\Forms;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\ViewAction::make(),
            Actions\Action::make('back')->label('Back')->color('warning')->url($this->getResource()::getUrl('index')),
            Actions\DeleteAction::make(),
        ];
    }
}

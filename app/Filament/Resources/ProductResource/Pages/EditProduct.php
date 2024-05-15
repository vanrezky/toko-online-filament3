<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use App\Models\Reseller;
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
            Actions\DeleteAction::make(),
        ];
    }

    // public Product $product;

    // public function mount($record): void
    // {
    //     $this->product = Product::with('resellerPrices')->findOrFail($record);
    //     parent::mount($record);
    // }

    // protected function getFormSchema(): array
    // {
    //     return static::getResource()::form($this->form)->getSchema();
    // }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }
}

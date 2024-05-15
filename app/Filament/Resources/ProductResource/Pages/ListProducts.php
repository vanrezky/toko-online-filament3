<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Constants\Status;
use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make()->label(__('All Product')),
            'Low Stock' => Tab::make()
                ->label(__('Low Stock'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereColumn('stock', '<=', 'security_stock')->where('stock', '>', Status::COUNT_OUT_OF_STOCK))
                ->badge(Product::query()->whereColumn('stock', '<=', 'security_stock')->where('stock', '>', Status::COUNT_OUT_OF_STOCK)->count()),
            'Out Of Stock' => Tab::make()
                ->label(__('Out Of Stock'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('stock', Status::COUNT_OUT_OF_STOCK))
                ->badge(Product::query()->where('stock', Status::COUNT_OUT_OF_STOCK)->count())
                ->badgeColor('danger'),

        ];
    }
}

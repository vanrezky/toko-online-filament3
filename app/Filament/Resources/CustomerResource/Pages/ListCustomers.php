<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\DistributorLevel;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->modalWidth(MaxWidth::Large),
        ];
    }

    public function getTabs(): array
    {

        $tabs = [
            'All' => Tab::make()->label(__('All')),
        ];

        $distributorLevels = DistributorLevel::all();

        foreach ($distributorLevels as $distributorLevel) {
            $tabs[$distributorLevel->name] = Tab::make()
                ->label($distributorLevel->name)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('distributor_level_id', $distributorLevel->id))
                ->badge(Customer::query()->where('distributor_level_id', $distributorLevel->id)->count());
        }

        return $tabs;
    }
}

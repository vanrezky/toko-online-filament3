<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\Reseller;
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
            'Normal' => Tab::make()->label(__('Normal'))
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->normalUser())
                ->badge(Customer::normalUser()->count()),
        ];

        $resellers = Reseller::active()->orderBy('level', 'ASC')->get();

        foreach ($resellers as $resel) {
            $tabs[$resel->name] = Tab::make()
                ->label($resel->name)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->resellerUser($resel->id))
                ->badge(Customer::resellerUser($resel->id)->count());
        }

        return $tabs;
    }
}

<?php

namespace App\Filament\Resources\BalanceResource\Pages;

use App\Filament\Resources\BalanceResource;
use App\Models\Balance;
use App\Models\Customer;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;

class CreateBalance extends CreateRecord
{
    protected static string $resource = BalanceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {

        $customer = Customer::find($data['customer_id']);
        $trxType = $data['trx_type'];
        $amount = $data['amount'];

        if ($trxType === '+') {
            $balance = $customer->balance + $amount;
            $customer->fill(['balance' => $balance]);
            $customer->save();
        } else {
            $customer->decrement('balance', $amount);
        }

        $nameUser = auth()->user()->name;
        $remark = $trxType === '+' ? __("Balance added by {$nameUser}") : __("Balance reduced by {$nameUser}");

        $data = (new Balance)->updateBalance($data['customer_id'], $amount, 0, $customer->balance, $trxType, $data['notes'], $remark);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VoucherType: int implements HasColor, HasIcon, HasLabel
{

    case PRODUCT = 1;
    case SHIPPING_COST = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PRODUCT => __('Product'),
            self::SHIPPING_COST => __('Shipping Cost'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::PRODUCT => 'success',
            self::SHIPPING_COST => 'danger',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PRODUCT => 'heroicon-o-tag',
            self::SHIPPING_COST => 'heroicon-o-truck',
        };
    }
}

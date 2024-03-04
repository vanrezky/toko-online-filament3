<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VoucherProductType: int implements HasColor, HasIcon, HasLabel
{

    case ALL_PRODUCT = 0;
    case PHYSICAL_PRODUCT = 1;
    case DIGITAL_PRODUCT = 2;


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ALL_PRODUCT => 'success',
            self::PHYSICAL_PRODUCT => 'info',
            self::DIGITAL_PRODUCT => 'warning',
        };
    }


    public function getIcon(): ?string
    {
        return match ($this) {
            self::ALL_PRODUCT => 'heroicon-o-tag',
            self::PHYSICAL_PRODUCT => 'heroicon-o-tag',
            self::DIGITAL_PRODUCT => 'heroicon-o-tag',
        };
    }


    public function getLabel(): ?string
    {
        return match ($this) {
            self::ALL_PRODUCT => __('All Product'),
            self::PHYSICAL_PRODUCT => __('Physical'),
            self::DIGITAL_PRODUCT => __('Digital'),
        };
    }
}

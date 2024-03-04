<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum VoucherDiscountType: int implements HasColor, HasIcon, HasLabel
{

    case FIXED = 0;
    case PERCENTAGE = 1;


    public function getColor(): string|array|null
    {
        return match ($this) {
            self::FIXED => 'success',
            self::PERCENTAGE => 'danger',
        };
    }


    public function getLabel(): ?string
    {
        return match ($this) {
            self::FIXED => __('Fixed'),
            self::PERCENTAGE => __('Percentage'),
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::FIXED => 'heroicon-o-tag',
            self::PERCENTAGE => 'heroicon-o-receipt-percent',
        };
    }
}

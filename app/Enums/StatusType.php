<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum StatusType: int implements HasColor, HasLabel, HasIcon
{

    case ACTIVE = 1;
    case INACTIVE = 0;


    public function getColor() :string|array|null {

        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }

    public function getIcon(): ?string {

        return match ($this) {
            self::ACTIVE => 'heroicon-o-check-circle',
            self::INACTIVE => 'heroicon-o-x-circle',
        };
    }

    public function getLabel():?string {

        return match ($this) {
            self::ACTIVE => __('Active'),
            self::INACTIVE => __('Inactive'),
        };
    }
}

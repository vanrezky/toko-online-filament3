<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductActiveStatus: int implements HasColor, HasIcon, HasLabel


{
    case DRAFT = 0;
    case PUBLISHED = 1;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::DRAFT => __('Draft'),
            self::PUBLISHED => __('Published'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::DRAFT => 'danger',
            self::PUBLISHED => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::DRAFT => 'heroicon-o-x-circle',
            self::PUBLISHED => 'heroicon-o-check-circle',
        };
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Z3d0X\FilamentLogger\Resources\ActivityResource as ResourcesActivityResource;

class ActivityResource extends ResourcesActivityResource
{
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return isSuperUser();
    }
}

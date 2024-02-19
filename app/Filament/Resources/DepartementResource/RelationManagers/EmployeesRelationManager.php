<?php

namespace App\Filament\Resources\DepartementResource\RelationManagers;

use App\Filament\Resources\EmployeeResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeesRelationManager extends RelationManager
{
    protected static string $relationship = 'employees';

    public function form(Form $form): Form
    {
        return EmployeeResource::form($form);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return EmployeeResource::infolist($infolist);
    }

    public function table(Table $table): Table
    {
        return EmployeeResource::table($table);
    }
}

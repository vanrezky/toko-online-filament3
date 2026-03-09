<?php

namespace App\Filament\Resources\FlashsaleResource\RelationManagers;

use App\Models\ProductFlashsale;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class ProductsRelationManager extends RelationManager
{
    protected static string $relationship = 'products';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('product_id')
                    ->relationship(
                        'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, RelationManager $livewire, ?Model $record) {
                            $flashsale = $livewire->getOwnerRecord();
                            $alreadyAddedProductIds = $flashsale->products()
                                ->when($record, fn($q) => $q->where('id', '!=', $record->id))
                                ->pluck('product_id');

                            return $query->whereNotIn('id', $alreadyAddedProductIds);
                        }
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->unique(
                        table: 'product_flashsales',
                        column: 'product_id',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule, RelationManager $livewire) {
                            return $rule->where('flashsale_id', $livewire->getOwnerRecord()->id);
                        }
                    ),
                TextInput::make('discount_percentage')
                    ->required()
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
                    ->numeric(),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->currencyMask(thousandSeparator: '.', decimalSeparator: ',', precision: 0)
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('product.name'),
                Tables\Columns\TextColumn::make('product.category.name'),
                Tables\Columns\TextColumn::make('stock'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}

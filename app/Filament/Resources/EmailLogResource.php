<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailLogResource\Pages;
use App\Models\EmailLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EmailLogResource extends Resource
{
    protected static ?string $model = EmailLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Master';

    protected static ?string $navigationLabel = 'Email Logs';

    protected static ?string $modelLabel = 'Email Log';

    protected static ?int $navigationSort = 3;

    protected static ?string $slug = 'email-logs';

    protected static bool $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('template_code')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('recipient_email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->limit(50),
                BadgeColumn::make('status')
                    ->colors([
                        'success' => 'sent',
                        'danger' => 'failed',
                        'warning' => 'pending',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state)),
                TextColumn::make('error_message')
                    ->limit(50)
                    ->tooltip(fn ($state) => $state),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'sent' => 'Sent',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\SelectFilter::make('template_code')
                    ->relationship('emailTemplate', 'code')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailLogs::route('/'),
            'view' => Pages\ViewEmailLog::route('/{record}'),
        ];
    }
}

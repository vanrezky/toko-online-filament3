<?php

namespace App\Filament\Resources\EmailLogResource\Pages;

use App\Filament\Resources\EmailLogResource;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailLog extends ViewRecord
{
    protected static string $resource = EmailLogResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Email Information')
                    ->schema([
                        TextEntry::make('template_code'),
                        TextEntry::make('recipient_email'),
                        TextEntry::make('subject'),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn ($state) => match ($state) {
                                'sent' => 'success',
                                'failed' => 'danger',
                                'pending' => 'warning',
                            }),
                    ])->columns(2),
                Section::make('Content')
                    ->schema([
                        CodeEntry::make('body')
                            ->label('Body Content')
                            ->language('html'),
                    ]),
                Section::make('Placeholders Used')
                    ->schema([
                        KeyValueEntry::make('placeholders'),
                    ]),
                Section::make('Metadata')
                    ->schema([
                        TextEntry::make('error_message'),
                        TextEntry::make('sent_at')
                            ->dateTime(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                    ])->columns(2),
            ]);
    }
}

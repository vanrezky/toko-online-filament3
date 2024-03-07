<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Constants\UploadPath;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Forms\Components as FormsComponents;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormsComponents\Section::make()
                    ->schema([
                        FormsComponents\FileUpload::make('image')
                            ->image()
                            ->directory(UploadPath::PROFILE_UPLOAD_PATH)
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])->columnSpanFull()
                            ->extraAttributes(['class' => 'circle']),

                    ])
                    ->columnSpan(1),

                FormsComponents\Group::make([
                    FormsComponents\Section::make()
                        ->schema([
                            FormsComponents\Group::make([
                                FormsComponents\TextInput::make('first_name')
                                    ->placeholder(__('e.g: ') . 'John')
                                    ->required()
                                    ->maxLength(100),
                                FormsComponents\TextInput::make('last_name')
                                    ->placeholder(__('e.g: ') . 'Smith')
                                    ->maxLength(100),
                                FormsComponents\TextInput::make('email')
                                    ->placeholder(__('e.g: ') . 'Johnsmith@example.com')
                                    ->email()
                                    ->required()
                                    ->maxLength(100)
                                    ->unique(ignoreRecord: true)
                                    ->disabled(),
                                FormsComponents\TextInput::make('username')
                                    ->placeholder(__('e.g: ') . 'johnsmith')
                                    ->minLength(6)
                                    ->maxLength(15)
                                    ->unique(ignoreRecord: true),
                                FormsComponents\TextInput::make('phone')
                                    ->placeholder(__('e.g: ') . '+6281234567890')
                                    ->tel()
                                    ->maxLength(20),
                                FormsComponents\TextInput::make('password')
                                    ->password()
                                    ->required()
                                    ->same('confirm_password')
                                    ->maxLength(255)
                                    ->visible(fn (string $operation): bool  => $operation === 'create'),
                                FormsComponents\TextInput::make('confirm_password')
                                    ->password()
                                    ->required()
                                    ->maxLength(255)
                                    ->visible(fn (string $operation): bool  => $operation === 'create'),
                            ])->columnSpanFull()->columns(2),

                        ])
                        ->columnSpan(2),


                    FormsComponents\Section::make('Password')
                        ->schema([
                            FormsComponents\TextInput::make('password')
                                ->password()
                                ->same('confirm_password')
                                ->maxLength(255),
                            FormsComponents\TextInput::make('confirm_password')
                                ->password()
                                ->maxLength(255),

                        ]),
                    FormsComponents\Section::make('Distributor Level')
                        ->schema([
                            FormsComponents\Select::make('distributor_level_id')
                                ->relationship('distributorLevel', titleAttribute: 'name')
                                ->searchable()
                                ->preload()

                        ]),


                ])->columnSpan(2)

            ])->columns(3);
    }
}

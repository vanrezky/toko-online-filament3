<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Constants\UploadPath;
use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Forms\Components as FormsComponents;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $record->update($data);

        return $record;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FormsComponents\Section::make(__('Personal Information'))
                    ->description(__('Manage Customer Information'))
                    ->schema([
                        FormsComponents\FileUpload::make('image')
                            ->label(__('Profil Image'))
                            ->avatar()
                            ->image()
                            ->directory(UploadPath::PROFILE_UPLOAD_PATH)
                            ->imageEditorAspectRatios([
                                '1:1',
                            ])
                            ->alignCenter()
                            ->columnSpan(1),
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
                        ])->columnSpan(2)->columns(2)
                    ])->columnSpanFull()->columns(3),

                FormsComponents\Section::make('Password')
                    ->schema([
                        FormsComponents\TextInput::make('password')
                            ->password()
                            ->same('confirm_password')
                            ->minLength(8)
                            ->maxLength(255),
                        FormsComponents\TextInput::make('confirm_password')
                            ->password()
                            ->maxLength(255),
                    ])->columnSpan(2),
                FormsComponents\Section::make('Distributor Level')
                    ->schema([
                        FormsComponents\Select::make('distributor_level_id')
                            ->relationship('distributorLevel', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                    ])->columnSpan(1),

            ])->columns(3);
    }
}

<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Navigation\MenuItem;
use Filament\Resources\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Panel;
use Filament\Forms\Form;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = CustomerResource::class;

    protected static string $view = 'filament.resources.customer-resource.pages.profile';

    public  ?Model $customer;

    public function getTitle(): string | Htmlable
    {
        return __('Profile') . ' ';
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            // ...
            ->userMenuItems([
                MenuItem::make()
                    ->label('Profile')
                    ->url(fn (): string => Profile::getUrl())
                    ->icon('heroicon-o-user'),
            ]);
        // ...
    }

    protected function getHeaderActions(): array
    {
        return [
            // Action::make('edit')
            //     ->url(route('posts.edit', ['post' => $this->post])),
            // Action::make('delete')
            //     ->requiresConfirmation()
            //     ->action(fn () => $this->post->delete()),

            // Action::make('approve')
            //     ->action(function (Post $record) {
            //         $record->approve();

            //         $this->refreshFormData([
            //             'status',
            //         ]);
            //     })
        ];
    }


    public function mount(Customer $customer): void
    {
        $this->customer = $customer;

        // $this->form->fill(
        //     auth()->user()->attributesToArray()
        // );
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('Update')
                ->color('primary')
                ->submit('Update'),
        ];
    }

    public function update()
    {
        auth()->user()->update(
            $this->form->getState()
        );
    }


    public function getGeneralSection(): Component
    {

        return Section::make('General')
            ->schema([
                TextInput::make('number')
                    ->live()
                    ->required()
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->autofocus()
                    ->required(),
                TextInput::make('email')
                    ->required(),
            ])
            ->statePath('data')
            ->model(auth()->user());
    }
}

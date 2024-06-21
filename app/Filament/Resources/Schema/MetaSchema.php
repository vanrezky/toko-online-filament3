<?php

namespace App\Filament\Resources\Schema;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class MetaSchema
{
    public static function get()
    {
        return Group::make()
            ->relationship('meta')
            ->schema([
                TextInput::make('title')->label(__('Meta Title'))
                    ->maxLength(60)
                    ->live(debounce: true)
                    ->helperText(function (string|null $state) {
                        $length = $state ? Str::length($state) : 0;
                        return "$length/160 characters";
                    })
                    ->afterStateUpdated(function (string|null $state, Component $component, Set $set) {
                        $length = $state ? Str::length($state) : 0;
                        $component->helperText("$length/60 characters");
                        $set('is_meta_changed_manually', true);
                    }),
                Hidden::make('is_meta_changed_manually')
                    ->default(false)
                    ->dehydrated(false),
                Textarea::make('description')
                    ->label(__('Meta Description'))
                    ->hint(__('Write an excerpt for your post'))
                    ->maxLength(160)
                    ->live(debounce: true)
                    ->helperText(function (string|null $state) {
                        $length = $state ? Str::length($state) : 0;
                        return "$length/160 characters";
                    })
                    ->afterStateUpdated(function (string|null $state, Component $component) {
                        $length = $state ? Str::length($state) : 0;
                        $component->helperText("$length/160 characters");
                    }),
                TextInput::make('keyword')->label(__('Meta Keywords'))
                    ->hint(__('Separate keywords with commas'))
                    ->live(debounce: true)
                    ->helperText(function (string|null $state) {
                        $length = $state ? count(Str::of($state)->explode(',')) : 0;
                        return "$length/5 keywords";
                    }),
            ]);
    }
}

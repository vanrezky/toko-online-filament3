<?php

namespace App\Filament\Resources\Schema;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class TitleSchema
{


    public static function title(string $name = 'title', bool|string $metagable = true)
    {
        return TextInput::make($name)
            ->live(onBlur: true)
            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, string $operation) use ($metagable) {
                if ($operation == 'edit')  return;
                if (!$get('is_slug_changed_manually') && filled($state)) {
                    $set('slug', Str::slug($state));
                }

                if ($metagable && !$get('meta.is_meta_changed_manually') && filled($state)) {
                    $metaName = is_bool($metagable) ? 'meta.title' : $metagable;
                    $set($metaName, Str::limit($state, 60, ''));
                }
            });
    }

    public static function slug($name = 'slug',)
    {
        return TextInput::make($name)
            ->maxLength(255)
            ->rules(['alpha_dash'])
            ->unique(ignoreRecord: true)
            ->afterStateUpdated(function (Set $set) {
                $set('is_slug_changed_manually', true);
            });
    }

    public static function hidden()
    {
        return Hidden::make('is_slug_changed_manually')
            ->default(false)
            ->dehydrated(false);
    }
}

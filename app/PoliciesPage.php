<?php

namespace App\Filament\FilamentPageTemplates;

use Beier\FilamentPages\Contracts\FilamentPageTemplate;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PoliciesPage implements FilamentPageTemplate
{
    public static function title(): string
    {
        return 'Policies Page';
    }

    public static function schema(): array
    {
        return [
            Card::make()
                ->schema([
                    TextInput::make('title')->required(),
                    Repeater::make('policies')
                    ->schema([
                        TextInput::make('title')->required(),
                        TinyEditor::make('description')->minHeight(300)->profile('custom')->required(),
                    ])->minItems(1),
                ]),
        ];
    }
}

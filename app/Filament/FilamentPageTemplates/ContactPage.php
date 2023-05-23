<?php

namespace App\Filament\FilamentPageTemplates;

use Beier\FilamentPages\Contracts\FilamentPageTemplate;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;

class ContactPage implements FilamentPageTemplate
{
    public static function title(): string
    {
        return 'Contact Page';
    }

    public static function schema(): array
    {
        return [
            Card::make()
                ->schema([
                    TextInput::make('main_title')->required(),
                    TextInput::make('title')->required(),
                    Placeholder::make('Order Information')->dehydrated(false),
                    TextInput::make('order_info_title')->required(),
                    TextInput::make('working_days')->required(),
                    TextInput::make('order_processing')->required(),
                    TextInput::make('delivery_info')->required(),
                    TextInput::make('contact_info_title')->required(),
                    TextInput::make('form_title')->required(),
                ]),
        ];
    }
}

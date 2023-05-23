<?php

namespace App\Filament\FilamentPageTemplates;

use Beier\FilamentPages\Contracts\FilamentPageTemplate;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class HomePage implements FilamentPageTemplate
{
    public static function title(): string
    {
        return 'Home Page';
    }

    public static function schema(): array
    {
        return [
            Card::make()
                ->schema([
                    Repeater::make('sliders')
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->required(),
                        TextInput::make('button_text')->required(),
                        TextInput::make('url')->url()->required(),
                        FileUpload::make('image')->image()
                        ->helperText('Must be Less than 10mb')->maxSize(10 * 1024) //10MB
                        ->enableDownload()
                        ->required(),
                    ])->minItems(1),
                    Repeater::make('info_text')
                    ->schema([
                        TextInput::make('title')->required(),
                        Textarea::make('description')->required(),
                        FileUpload::make('image')->image()
                        ->helperText('Must be Less than 10mb')->maxSize(10 * 1024) //10MB
                        ->directory('uploads')
                        ->required(),
                    ])->minItems(4)->maxItems(4),
                    TextInput::make('personalization_title')->required(),
                    Textarea::make('personalization_description')->required(),
                    TextInput::make('personalization_subtitle')->required(),
                    TextInput::make('request_title')->required(),
                    Textarea::make('request_description')->required(),
                ]),
        ];
    }
}

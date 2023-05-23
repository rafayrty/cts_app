<?php

namespace App\Filament\FilamentPageTemplates;

use Beier\FilamentPages\Contracts\FilamentPageTemplate;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class AboutPage implements FilamentPageTemplate
{
    public static function title(): string
    {
        return 'About Page';
    }

    public static function schema(): array
    {
        return [
            Card::make()
                ->schema([
                    TextInput::make('main_title')->required(),
                    FileUpload::make('image')->image()
                        ->helperText('Must be Less than 10mb')->maxSize(10 * 1024) //10MB
                        ->directory('uploads')
                        ->required(),
                    TextInput::make('title')->required(),
                    TextInput::make('procedure_title')->required(),
                    Repeater::make('procedure')
                    ->schema([
                        FileUpload::make('image')->image()
                        ->helperText('Must be Less than 10mb')->maxSize(10 * 1024) //10MB
                        ->directory('uploads')
                        ->required(),
                        Textarea::make('description')->required(),
                    ])->orderable()->minItems(1),
                    TextInput::make('description_title')->required(),
                    Textarea::make('description')->required(),
                ]),
        ];
    }
}

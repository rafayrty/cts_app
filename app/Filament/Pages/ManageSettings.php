<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;

class ManageSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationGroup = 'Administration';

    protected static string $settings = GeneralSettings::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //$data['is_published'] = true;

        //Delete the autosaved data
        //$product = Product::where('slug', $data['slug'])->get()->first();
        //$document = Document::where('product_id', $product->id)->get()->first();
        //Document::findOrFail($document->id)->delete();
        //Product::findOrFail($product->id)->delete();

        dd($data);
        return $data;
    }
    protected function getFormSchema(): array
    {
        return [
            Card::make()
                ->schema([
                    Grid::make(2)->schema([
                        TextInput::make('promotion_text')
                            ->label('Promotion Text')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->required(),
                        TextInput::make('promotion_link')
                            ->label('Promotion Link')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->url()
                            ->required(),
                        TextInput::make('address')
                            ->label('Address')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->required(),
                        TextInput::make('phone')
                            ->label('Phone')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->required(),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->extraAttributes(['dir' => 'rtl'])
                            ->required(),
                        Textarea::make('about')
                            ->label('About')
                            ->extraAttributes(['dir' => 'rtl'])
                            ->required(),
                    ]),

                    TextInput::make('shipping_fee')
                        ->label('Shipping Fee')
                        ->required(),
                    Repeater::make('faqs')
                        ->schema([
                            TextInput::make('heading')
                                ->extraAttributes(['dir' => 'rtl'])
                                ->required(),
                            Textarea::make('description')
                                ->extraAttributes(['dir' => 'rtl'])
                                ->required(),
                        ]),
                    Repeater::make('social_medias')
                        ->schema([
                            FileUpload::make('icon')->acceptedFileTypes(['image/svg+xml'])
                                ->helperText('Icon Must be of an SVG Type')->maxSize(1 * 1024) //1MB
                                ->directory('uploads')
                                ->required(),
                            TextInput::make('url')
                                ->url()
                                ->required(),
                        ]),
                ]),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DedicationResource\Pages;
use App\Models\Dedication;
use Filament\Forms;
use Filament\Forms\Components\Placeholder;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\HtmlString;

class DedicationResource extends Resource
{
    protected static ?string $model = Dedication::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Placeholder::make('dedication_info')->content(new HtmlString('<h1 class="w-full bg-gray-200 p-2 rounded-md font-semibold dark:bg-gray-900">
                                  Add a Dedication to be shown on the books add {basmti} where you want the text to be replaced
                                  </h1>'))->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->extraAttributes(['dir' => 'rtl'])
                    ->required(),
                Forms\Components\TextArea::make('dedication')
                    ->extraAttributes(['dir' => 'rtl'])
                    ->rows(10)
                    ->columnSpanFull()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->limit(20),
                Tables\Columns\TextColumn::make('dedication')->limit(45),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDedications::route('/'),
        ];
    }
}

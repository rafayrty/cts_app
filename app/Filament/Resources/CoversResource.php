<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoversResource\Pages;
use App\Models\Covers;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class CoversResource extends Resource
{
    protected static ?string $model = Covers::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Product Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->extraAttributes(['dir' => 'rtl'])
                    ->required()
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')->extraAttributes(['dir' => 'rtl']),
                FileUpload::make('image')
                    ->image()
                    ->required()
                    ->directory('uploads'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\ImageColumn::make('image'),
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
            'index' => Pages\ManageCovers::route('/'),
        ];
    }
}

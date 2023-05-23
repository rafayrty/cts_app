<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FormSubmissionsResource\Pages;
use App\Models\FormSubmissions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class FormSubmissionsResource extends Resource
{
    protected static ?string $model = FormSubmissions::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('created_at', '>', now()->subDay(1))->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                ViewField::make('form_submission')->view('forms.form_content')->dehydrated(false),
                TextInput::make('id')->hidden(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('form'),
                Tables\Columns\TextColumn::make('content.name')->label('Name'),
                Tables\Columns\TextColumn::make('content.email')->label('Email'),
                Tables\Columns\TextColumn::make('content.phone')->label('Phone'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('View'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFormSubmissions::route('/'),
            //'create' => Pages\CreateFormSubmissions::route('/create'),
            //'edit' => Pages\EditFormSubmissions::route('/{record}/edit'),
        ];
    }
}

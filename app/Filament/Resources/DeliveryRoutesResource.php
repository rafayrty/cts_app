<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DeliveryRoutesResource\Pages;
use App\Models\DeliveryRoutes;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class DeliveryRoutesResource extends Resource
{
    protected static ?string $model = DeliveryRoutes::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('route_name')->required(),
                Forms\Components\TextInput::make('from')->required(),
                Forms\Components\TextInput::make('to_lat')->required(),
                Forms\Components\TextInput::make('to_long')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('route_name')->sortable(),
                Tables\Columns\TextColumn::make('from')->searchable(),
                Tables\Columns\TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDeliveryRoutes::route('/'),
            'create' => Pages\CreateDeliveryRoutes::route('/create'),
            'edit' => Pages\EditDeliveryRoutes::route('/{record}/edit'),
        ];
    }
}

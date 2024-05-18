<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClassRoomResource\Pages;
use App\Models\ClassRoom;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\PasswordInput;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ClassRoomResource extends Resource
{
    protected static ?string $model = ClassRoom::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('class_name')
                    ->required()
                    ->maxLength(255),
                //Hash

            //Select::make('filament_users')
                //->multiple()
                //->preload()
                //->options(FilamentUser::all()->pluck('email', 'id'))->searchable()->required()
                //->searchable(),
                Forms\Components\Toggle::make('is_published')
                ->hidden(fn () => Auth::user()->hasRole('Student'))
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('class_name'),
                Tables\Columns\TextColumn::make('created_by'),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
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
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ClassRoomResource\RelationManagers\ContentsRelationManager::class,
            ClassRoomResource\RelationManagers\CommentsRelationManager::class,
            ClassRoomResource\RelationManagers\UsersRelationManager::class,
            ClassRoomResource\RelationManagers\QuizzesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClassRooms::route('/'),
            'create' => Pages\CreateClassRoom::route('/create'),
            'edit' => Pages\EditClassRoom::route('/{record}/edit'),
            'view' => Pages\ViewClassRoom::route('/{record}'),
        ];
    }
}

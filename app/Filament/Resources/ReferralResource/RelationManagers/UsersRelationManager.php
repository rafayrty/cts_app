<?php

namespace App\Filament\Resources\ReferralResource\RelationManagers;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Tables\Actions\AttachAction;
use Filament\Forms;
use App\Filament\Resources\UserResource;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),

                //Tables\Actions\AttachAction::make(),
                Action::make('create_user')
                ->url(Pages\CreateUser::route('/admin/users/create')['route']),
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),

                Action::make('edit')
                ->url(fn (User $record): string => UserResource::getUrl('edit', ['record' => $record])),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

<?php

namespace App\Filament\Resources\ClassRoomResource\RelationManagers;

use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $label = 'Student';

    protected static ?string $pluralLabel = 'Students';

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('user_id')->label('Add Student')
                    ->extraAttributes(['dir' => 'rtl'])->preload()
                    ->options(FilamentUser::all()->pluck('email', 'id'))->searchable()->required(),
                //Forms\Components\TextInput::make('')
                //->required()
                //->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email')->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\AssociateAction::make()->preloadRecordSelect(),
                Tables\Actions\AttachAction::make()->preloadRecordSelect()
                ->recordSelectOptionsQuery(function (EloquentBuilder $query) {
                        $query->whereHas('roles', function ($query) {
                            $query->where('name', 'student');
                        });
                    })

                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([

                Tables\Actions\DetachAction::make(),
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

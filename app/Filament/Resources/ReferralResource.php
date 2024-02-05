<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReferralResource\Pages;
use App\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Card;
use App\Models\Referral;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ReferralResource extends Resource
{
    protected static ?string $model = Referral::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('updated_at')->label('Total Users')->getStateUsing(function (Model $record) {
                    $referral = $record;

                    return $referral->users()->count();
                }),

                Tables\Columns\TextColumn::make('referral.users.name')->label('Total Orders')->getStateUsing(function (Model $record) {
                    $referral = $record;

                    $users = $referral->users()->get();

                    $user_ids = $users->map(fn ($user)=>$user->id)->toArray();
                    $totalAmount = Order::whereIn('user_id',$user_ids)->count();

                    return $totalAmount;
                }),
                Tables\Columns\TextColumn::make('referral.users.email')->label('Order Total')->getStateUsing(function (Model $record) {
                    $referral = $record;

                    $users = $referral->users()->get();

                    $user_ids = $users->map(fn ($user)=>$user->id)->toArray();
                    $totalAmount = Order::whereIn('user_id',$user_ids)->sum('total');

                    return $totalAmount;
                })->money('ils'),

                Tables\Columns\TextColumn::make('id')->label('Link')->html()->getStateUsing(function (Model $record) {
                    $referral = $record;
                    return "<a href='https://basmti.com?ref=".$referral->name."' class='
                        filament-link inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action'>
                        Share Link</a>";
                }),
                Tables\Columns\TextColumn::make('created_at')
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

    public static function getRelations(): array
    {
        return [
            ReferralResource\RelationManagers\UsersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReferrals::route('/'),
            'create' => Pages\CreateReferral::route('/create'),
            'edit' => Pages\EditReferral::route('/{record}/edit'),
        ];
    }
}

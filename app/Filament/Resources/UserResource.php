<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\Referral;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
//use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;

use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Services\RegisterService;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $navigationLabel = 'Customers';

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->unique(ignorable: fn ($record) => $record)
                            ->required()
                            ->maxLength(255),
                        //Forms\Components\DateTimePicker::make('email_verified_at'),
                        Forms\Components\DateTimePicker::make('verified_at'),
                        Select::make('country_code')->searchable()->options(RegisterService::$countries_phone_list)->required(),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create')
                            ->maxLength(255),
                    ]),
                ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->headerActions(
                [
                    ExportAction::make(),
                    ExportAction::make('export_w_orders')->label('Export W Orders')->exports([
                        ExcelExport::make()
                        ->fromTable()
                        ->modifyQueryUsing(function ($query) {
                        return $query->join('orders', 'users.id', '=', 'orders.user_id')
                            ->select('users.*')
                            ->distinct();
                        })
                    ])
                ]
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')->label("Order Count")->getStateUsing(function (Model $record) {
                    $item = $record;
                   $user_count =  \App\Models\Order::where('user_id',$item->id)->count();

                    return $user_count;
                }),
                Tables\Columns\TextColumn::make('full_name'),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->copyable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                //Tables\Columns\TextColumn::make('updated_at')
                //->dateTime(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('referral_id')->label("Referral")
                    ->options(Referral::all()->pluck('name', 'id')),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()
            ]);
    }

    public static function getWidgets(): array
    {
        return [
        ];
    }

    public static function getRelations(): array
    {
        return [
            UserResource\RelationManagers\OrdersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

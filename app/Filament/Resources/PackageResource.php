<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PackageResource\Pages;
use App\Models\DeliveryRoutes;
use App\Models\Package;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\ViewColumn;
use Illuminate\Database\Eloquent\Model;

class PackageResource extends Resource
{
    protected static ?string $model = Package::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //Forms\Components\TextInput::make('delivery_routes_id')
                //->required(),
                Select::make('delivery_routes_id')->relationship('delivery_routes', 'route_name')->label('Delivery Route')->required()
                    ->preload()
                    ->searchable(),
                Select::make('status')->label('Status')->required()
                    ->options([
                        'pending' => 'Pending',
                        'delivering' => 'Delivering',
                        'delivered' => 'Delivered',
                    ]
                    )
                    ->searchable(),
                Forms\Components\TextInput::make('weight')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('price')->placeholder('Rs1 = 100')->numeric()->minValue(1)->required(),


            ]);
    }

    public static function table(Table $table): Table
    {

        if(auth()->user()->can('packages.view')){
            $columns = [

                    Tables\Columns\TextColumn::make('id'),
                    Tables\Columns\TextColumn::make('delivery_routes_id')->label('Delivery Route')->getStateUsing(function (Package $record) {
                        return DeliveryRoutes::find($record->delivery_routes_id)->from;
                    }),
                    //Tables\Columns\TextColumn::make('lat')->label('Track Package')->html()->getStateUsing(function (Model $record) {
                     //return "<a href='admin/package/package_tracking/".$record->id."' class='
                        //filament-link inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action'>
                        //Track Package</a>";
                    //}),
                    Tables\Columns\TextColumn::make('status')->label('Status')->searchable()->hidden(fn()=>!auth()->user()->can('packages.viewStatus')),
                    Tables\Columns\TextColumn::make('weight'),
                    Tables\Columns\TextColumn::make('description'),
                    Tables\Columns\TextColumn::make('price')->money('pkr')->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime(),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()];
        }else if(auth()->user()->can('packages.viewStatus')){
            $columns = [

                    Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('status')->label('Status')->searchable()];
        }else{
            $columns = [
                    Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('delivery_routes_id')->label('Delivery Route')->getStateUsing(function (Package $record) {
                        return DeliveryRoutes::find($record->delivery_routes_id)->from;
                    }),


                //Tables\Columns\TextColumn::make('updated_at')->label('Track Package')->html()->getStateUsing(function (Model $record) {
                    //return "<a href='admin/package/package_tracking/".$record->id."' class='
                        //filament-link inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action'>
                        //Share Link</a>";
                //}),


                    Tables\Columns\TextColumn::make('status')->label('Status')->searchable()->hidden(fn()=>!auth()->user()->can('packages.viewStatus')),
                    Tables\Columns\TextColumn::make('weight'),
                    Tables\Columns\TextColumn::make('description'),
                    Tables\Columns\TextColumn::make('price')->money('pkr')->sortable(),
                    Tables\Columns\TextColumn::make('created_at')
                        ->dateTime(),
                    Tables\Columns\TextColumn::make('updated_at')
                        ->dateTime()];
        }
        return $table
            ->columns($columns)
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
            'index' => Pages\ListPackages::route('/'),
            'package_tracking' => Pages\PackageTracking::route('/package_tracking/{id?}'),
            'create' => Pages\CreatePackage::route('/create'),
            'edit' => Pages\EditPackage::route('/{record}/edit'),
        ];
    }
}

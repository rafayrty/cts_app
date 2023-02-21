<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $recordTitleAttribute = 'order_numeric_id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('order_numeric_id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_numeric_id')->label('Order ID#')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('sub_total')->label('Subtotal')->money('ils')->sortable(),
                Tables\Columns\TextColumn::make('shipping')->label('Shipping')->money('ils')->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'primary' => 'DELIVERED',
                        'secondary' => 'PENDING',
                        'warning' => 'PROCESSING',
                        'info' => 'DELIVERING',
                        'danger' => 'CANCELLED',
                    ]),
                //Tables\Columns\TextColumn::make('total')->label('Total')->money('eur')->sortable(),
                Tables\Columns\TextColumn::make('total')->label('Total')->money('ils')->sortable(),
                Tables\Columns\TextColumn::make('items.barcode_number')->label('Barcodes')->hidden()->searchable(),
                Tables\Columns\TextColumn::make('items_count')->counts('items')->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
            ])
             ->filters([
                 SelectFilter::make('status')
                     ->options([
                         'DELIVERED' => 'Delivered',
                         'PENDING' => 'Pending',
                         'PROCESSING' => 'Processing',
                         'DELIVERING' => 'Delivering',
                         'CANCELLED' => 'Cancelled',
                     ]),
                 Filter::make('barcode')
                     ->form([
                         Forms\Components\TextInput::make('barcode'),
                     ]),
             ])
            ->filters([
                //
            ])
            ->headerActions([
                //Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Action::make('generate_pdf')
                ->url(fn (Order $record): string => OrderResource::getUrl('generate_pdf', ['id' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}

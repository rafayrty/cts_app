<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Services\RegisterService;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationGroup = 'Order Management';

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    public static function canCreate(): bool
    {
        return false;
    }

     public static function form(Form $form): Form
     {
         return $form
             ->schema([
                 Grid::make(4)->schema([
                     Card::make([
                         ViewField::make('order_info')->view('forms.order_info'),
                     ])->columnSpan(1),
                     Card::make([
                         Grid::make(3)->schema([
                             TextInput::make('order_numeric_id')->label('Order ID #')->disabled(true),
                             TextInput::make('total')->disabled(true),
                             TextInput::make('sub_total')->disabled(true),
                             TextInput::make('shipping')->disabled(true),
                             Select::make('status')->options([
                                 'DELIVERED' => 'Delivered',
                                 'PENDING' => 'Pending',
                                 'PROCESSING' => 'Processing',
                                 'DELIVERING' => 'Delivering',
                                 'CANCELLED' => 'Cancelled',
                             ])->required(),

                             Select::make('payment_status')->options([
                                 'FAILED' => 'Failed',
                                 'PENDING' => 'Pending',
                                 'COMPLETED' => 'Completed',
                             ])->required(),
                             TextInput::make('coupon')->disabled(true),
                             TextInput::make('discount_total')->disabled(true),
                         ]),
                         Fieldset::make('Address')
                                 ->schema([
                                     TextInput::make('address.first_name')->required(),
                                     TextInput::make('address.last_name')->required(),
                                     TextInput::make('address.email')->required(),
                                     Grid::make(4)->schema([
                                         TextInput::make('address.country_code')->required(),
                                         TextInput::make('address.phone')->required(),
                                         Select::make('address.country')->searchable()->options(RegisterService::$countries_static_list)->required(),
                                         TextInput::make('address.state')->required(),
                                     ]),
                                     TextInput::make('address.street')->required(),
                                     TextInput::make('address.apartment_no')->required(),
                                     Textarea::make('address.message')->rows(2),
                                 ])->columns(3),
                     ])->columnSpan(3),
                 ]),
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
                 //Tables\Columns\TextColumn::make('barcodes')->searchable(),
                 //->searchable(query:function (Builder $query, string $search): Builder {
                 //return $query->whereJsonContains('barcodes->*', ['barcode_number' => $search]);
                 //}),
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

                 Filter::make('barcodes')
                     ->form([
                         Forms\Components\TextInput::make('barcodes'),
                     ])
                   ->query(function (Builder $query, array $data): Builder {
                       return $query
                           ->when(
                               $data['barcodes'],
                               function (Builder $query, $data): Builder {
                                   return $query->whereJsonContains('barcodes', ['barcode_number' => $data]);
                               }
                           );
                   }),
                 //Filter::make('barcodes')
                 //->form([
                 //Forms\Components\TextInput::make('barcodes'),
                 //])
                 //->query(function (Builder $query, array $data): Builder {
                 //return $query->whereJsonContains('barcodes->*', ['barcode_number' => $data])->get();
                 //}),
             ])
             ->actions([
                 //Tables\Actions\ViewAction::make(),
                 Tables\Actions\EditAction::make(),
                 Tables\Actions\DeleteAction::make(),
                 Action::make('generate_pdf')
                 ->url(fn (Order $record): string => OrderResource::getUrl('generate_pdf', ['id' => $record->id])),
             ])
             ->bulkActions([
                 Tables\Actions\DeleteBulkAction::make(),
             ]);
     }

     public static function getRelations(): array
     {
         return [
             OrderResource\RelationManagers\ItemsRelationManager::class,
         ];
     }

     public static function getPages(): array
     {
         return [
             'index' => Pages\ListOrders::route('/'),
             //'create' => Pages\CreateOrder::route('/create'),
             //'view' => Pages\ViewOrder::route('/{record}'),
             //'order_scan' => Pages\OrderScan::route('/order_scan'),
             'edit' => Pages\EditOrder::route('/{record}/edit'),
             'generate_pdf' => Pages\GeneratePDFPage::route('/generate_pdf/{id}'),
         ];
     }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Actions\Orders\CalculationsOrder;
use App\Filament\Resources\Actions\Orders\ProcessOrderInfo;
use App\Filament\Resources\OrderResource\Pages;
use App\Models\Coupon;
use App\Models\Covers;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\RegisterService;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
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

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public function __construct(RegisterService $registerService)
    {
        $this->registerService = $registerService;
    }

    //public static function canCreate(): bool
    //{
        //return false;
    //}

     public static function form(Form $form): Form
     {
         return $form
             ->schema([
                 ViewField::make('time_info')->view('forms.timer')->columnSpan(4)->dehydrated(false)->hiddenOn('create'),
                 Grid::make(4)->schema([
                     Card::make([
                         ViewField::make('order_info')->view('forms.order_info')->dehydrated(false),
                     ])->columnSpan(1),
                     Card::make([
                         Grid::make(3)->schema([
                             TextInput::make('order_numeric_id')->label('Order ID #')->disabled(true),
                             TextInput::make('total')->disabled(true)->afterStateHydrated(fn (Closure $set, $state) => $set('total', $state / 100))->dehydrated(false),
                             TextInput::make('sub_total')->disabled(true)->afterStateHydrated(fn (Closure $set, $state) => $set('sub_total', $state / 100))->dehydrated(false),
                             TextInput::make('shipping')->disabled(true)->afterStateHydrated(fn (Closure $set, $state) => $set('shipping', $state / 100))->dehydrated(false),
                             Select::make('print_house_status')->options([
                                 'starting printing' => 'Starting Printing',
                                 'packaging' => 'Packaging',
                                 'ready for delivery' => 'Ready For Delivery',
                                 'done' => 'Done',
                                 'stuck' => 'Stuck',
                             ])->required(),
                             Select::make('client_status')->options([
                                 'starting printing' => 'Starting Printing',
                                 'packaging' => 'Packaging',
                                 'ready for delivery' => 'Ready For Delivery',
                                 'done' => 'Done',
                                 'cancel' => 'Cancel',
                             ])->required(),
                             Select::make('payment_status')->options([
                                 'FAILED' => 'Failed',
                                 'PENDING' => 'Pending',
                                 'COMPLETED' => 'Completed',
                             ])->required(),
                             TextInput::make('coupon')->disabled(true),
                             TextInput::make('discount_total')->disabled(true)->afterStateHydrated(fn (Closure $set, $state) => $set('discount_total', $state / 100)),
                         ]),
                         Fieldset::make('Address')
                                 ->schema([
                                     TextInput::make('address.first_name')->required(),
                                     TextInput::make('address.last_name')->required(),
                                     Grid::make(4)->schema([
                                         TextInput::make('address.phone')->required(),
                                         TextInput::make('address.country_code')->required(),
                                         TextInput::make('address.backup_phone'),
                                         TextInput::make('address.backup_country_code'),
                                         //Select::make('address.country')->searchable()->options(RegisterService::$countries_static_list)->required(),
                                     ]),
                                     TextInput::make('address.city')->required(),
                                     TextInput::make('address.street_name')->required(),
                                     TextInput::make('address.street_number')->required(),
                                     TextInput::make('address.home_no')->required(),
                                     Textarea::make('address.message')->rows(2),
                                 ])->columns(3),
                     ])->columnSpan(3),
                 ])->hiddenOn('create'),
                 ViewField::make('order_pdf')->view('forms.order_pdf')->columnSpan(4)->dehydrated(false)->hiddenOn('create'),

                 //Order Creation Form
                 Card::make([
                     Select::make('user_id')->label('Customer')
                            ->extraAttributes(['dir' => 'rtl'])->preload()
                            ->options(User::all()->pluck('email', 'id'))->searchable()->required(),
                     Select::make('coupon')->label('Coupon')
                            ->preload()
                            ->options(Coupon::all()->pluck('coupon_name', 'id'))->searchable()->reactive(),
                     Repeater::make('items')->schema([
                         Select::make('product_id')->label('Product Name')
                        ->extraAttributes(['dir' => 'rtl'])->preload()
                        ->afterStateUpdated(Closure::fromCallable(new CalculationsOrder()))
                        ->options(Product::all()->pluck('product_name', 'id'))->searchable()->required()->reactive(),
                         Grid::make(3)->schema([
                             TextInput::make('name')->required(),
                             TextInput::make('age')->helperText('Only add if exists'),
                             TextInput::make('first_letter')->helperText('Only add if exists'),
                         ]),
                         Select::make('cover_id')->label('Cover')
                                 ->extraAttributes(['dir' => 'rtl'])->preload()
                                 ->afterStateUpdated(Closure::fromCallable(new CalculationsOrder()))
                                 ->options(Covers::all()->pluck('name', 'id'))->searchable()->required()->reactive(),
                         Textarea::make('dedication')->required()->maxLength(500),
                     ])->collapsible()->cloneable(),
                     Hidden::make('sub_total')->default(0)->disabled(),
                     Fieldset::make('Address')
                                 ->schema([
                                     TextInput::make('address.first_name')->required(),
                                     TextInput::make('address.last_name')->required(),
                                     Grid::make(4)->schema([
                                         TextInput::make('address.phone')->required(),
                                         TextInput::make('address.country_code')->required(),
                                         TextInput::make('address.backup_phone'),
                                         TextInput::make('address.backup_country_code'),
                                         //Select::make('address.country')->searchable()->options(RegisterService::$countries_static_list)->required(),
                                     ]),
                                     TextInput::make('address.city')->required(),
                                     TextInput::make('address.street_name')->required(),
                                     TextInput::make('address.street_number')->required(),
                                     TextInput::make('address.home_no')->required(),
                                     Textarea::make('address.message')->rows(2),
                                 ])->columns(3),

                     Checkbox::make('card_payment')->label('Card Payment')->reactive(),
                     Grid::make(3)->schema([
                         TextInput::make('cardHolderName')->required(),
                         TextInput::make('creditCard')->mask(fn (TextInput\Mask $mask) => $mask->pattern('0000 0000 0000 0000'))->required(),
                         TextInput::make('expiry')->mask(fn (TextInput\Mask $mask) => $mask->pattern('00/00'))->required(),
                         TextInput::make('cvv')->maxLength(4)->required(),
                         TextInput::make('postcode')->maxLength(10)->required(),
                         TextInput::make('id_number')->maxLength(10)->required(),
                     ])->hidden(function (Closure $get) {
                         if ($get('card_payment') == '1') {
                                 return false;
                         }
                         return true;
                     })->reactive(),
                     Placeholder::make('Payment Info')->content(Closure::fromCallable(new ProcessOrderInfo()))->reactive(),
                 ])->hiddenOn('edit'),
             ]);
     }

     public static function table(Table $table): Table
     {
         return $table
             ->columns([
                 Tables\Columns\TextColumn::make('order_numeric_id')->label('Order ID#')->sortable()->searchable(),
                 Tables\Columns\TextColumn::make('user.full_name')->label('Client Name')->sortable()->searchable(),
                 Tables\Columns\TextColumn::make('sub_total')->label('Subtotal')->money('ils')->sortable(),
                 Tables\Columns\TextColumn::make('shipping')->label('Shipping')->money('ils')->sortable(),
                 Tables\Columns\BadgeColumn::make('print_house_status')
                     ->colors([
                         'primary' => 'starting printing',
                         'secondary' => 'packaging',
                         'warning' => 'ready for delivery',
                         'info' => 'done',
                         'danger' => 'stuck',
                         'danger' => 'cancel',
                     ]),
                 Tables\Columns\BadgeColumn::make('client_status')
                     ->colors([
                         'primary' => 'starting printing',
                         'secondary' => 'packaging',
                         'warning' => 'ready for delivery',
                         'info' => 'done',
                         'danger' => 'stuck',
                         'danger' => 'cancel',
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
                 SelectFilter::make('print_house_status')
                     ->options([
                         'starting printing' => 'Starting Printing',
                         'packaging' => 'Packaging',
                         'ready for delivery' => 'Ready For Delivery',
                         'done' => 'Done',
                         'stuck' => 'Stuck',
                     ]),
                 SelectFilter::make('client_status')
                     ->options([
                         'starting printing' => 'Starting Printing',
                         'packaging' => 'Packaging',
                         'ready for delivery' => 'Ready For Delivery',
                         'done' => 'Done',
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

                 Action::make('update_status')
                     ->action(function (Order $record, array $data): void {
                         $record->print_house_status = $data['print_house_status'];
                         $record->client_status = $data['client_status'];
                         $record->save();
                     })
                     ->mountUsing(fn (Forms\ComponentContainer $form, Order $record) => $form->fill([
                         'print_house_status' => $record->print_house_status,
                         'client_status' => $record->client_status,
                     ]))
                     ->form([
                         Select::make('print_house_status')->options([
                             'starting printing' => 'Starting Printing',
                             'packaging' => 'Packaging',
                             'ready for delivery' => 'Ready For Delivery',
                             'done' => 'Done',
                             'stuck' => 'Stuck',
                         ])->required(),
                         Select::make('client_status')->options([
                             'starting printing' => 'Starting Printing',
                             'packaging' => 'Packaging',
                             'ready for delivery' => 'Ready For Delivery',
                             'done' => 'Done',
                         ])->required(),
                     ]),
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
             'create' => Pages\CreateOrder::route('/create'),
             //'view' => Pages\ViewOrder::route('/{record}'),
             //'order_scan' => Pages\OrderScan::route('/order_scan'),
             'edit' => Pages\EditOrder::route('/{record}/edit'),
             'generate_pdf' => Pages\GeneratePDFPage::route('/generate_pdf/{id}'),
         ];
     }
}

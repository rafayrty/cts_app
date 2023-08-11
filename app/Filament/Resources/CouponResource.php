<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CouponResource\Pages;
use App\Models\Coupon;
use App\Models\Order;
use Closure;
use Illuminate\Support\Facades\URL;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('coupon_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('min_amount')
                            ->numeric()
                            ->minValue(0)
                    ->required(),
                    Forms\Components\TextInput::make('commission_percentage')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->required(),
                    Forms\Components\Checkbox::make('free_shipping')->label('Free Shipping Coupon')->helperText('Discount % won\'t be used then'),
                    Forms\Components\TextInput::make('discount_percentage')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(100)
                            ->required(),
                        Forms\Components\DatePicker::make('expiry')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('min_amount'),
                Tables\Columns\TextColumn::make('coupon_name'),
                Tables\Columns\TextColumn::make('times_used'),
                Tables\Columns\TextColumn::make('expiry')
                    ->date(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')->label('Sales Generated')->getStateUsing(function (Model $record) {
                    $coupon = $record;
                    $totalSalesAmount = Order::where('coupon', $coupon->coupon_name)->sum('total');
                    return $totalSalesAmount;
                })->money('ils'),
                Tables\Columns\TextColumn::make('commission_percentage'),
                Tables\Columns\TextColumn::make('id')->label('Link')->html()->getStateUsing(function (Model $record) {
                    $coupon = $record;
                    $url = URL::signedRoute('influencer.dashboard',$coupon->coupon_name);

                    return "<a href='".$url."' class='
                        filament-link inline-flex items-center justify-center font-medium outline-none hover:underline focus:underline text-sm text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400 filament-tables-link-action'>
                        Share Link</a>";
                })
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
            'index' => Pages\ListCoupons::route('/'),
            'create' => Pages\CreateCoupon::route('/create'),
            'edit' => Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use App\Models\Order;
use Closure;
use Filament\Tables;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class LatestPromotions extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        return Coupon::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
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
        ];
    }
}

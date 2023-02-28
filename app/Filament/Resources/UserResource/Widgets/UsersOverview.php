<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class UsersOverview extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Total Customers', User::count()),
            Card::make('Total Products', Product::count()),
            Card::make('Total Orders', Order::count()),
            Card::make('Total Sales', "₪".Order::sum('total')),
        ];
    }
}

<?php

namespace App\Filament\Resources\OrderResource\Pages;

use Closure;
use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStatusFilter;
use App\Filament\Widgets\PopularProductsChart;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatusFilter::class,
        ];
    }

protected function getTableRecordActionUsing(): ?Closure
{
    return null;
}
protected function getHeaderWidgetsColumns(): int | array
{
    return 1;
}


}


<?php

namespace App\Filament\Resources\DeliveryRoutesResource\Pages;

use App\Filament\Resources\DeliveryRoutesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryRoutes extends ListRecords
{
    protected static string $resource = DeliveryRoutesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

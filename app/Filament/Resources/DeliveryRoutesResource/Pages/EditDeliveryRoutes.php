<?php

namespace App\Filament\Resources\DeliveryRoutesResource\Pages;

use App\Filament\Resources\DeliveryRoutesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryRoutes extends EditRecord
{
    protected static string $resource = DeliveryRoutesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\DedicationResource\Pages;

use App\Filament\Resources\DedicationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageDedications extends ManageRecords
{
    protected static string $resource = DedicationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\CoversResource\Pages;

use App\Filament\Resources\CoversResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCovers extends ManageRecords
{
    protected static string $resource = CoversResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\FontsResource\Pages;

use App\Filament\Resources\FontsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageFonts extends ManageRecords
{
    protected static string $resource = FontsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

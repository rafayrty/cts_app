<?php

namespace App\Filament\Resources\NewProductResource\Pages;

use App\Filament\Resources\NewProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNewProducts extends ListRecords
{
    protected static string $resource = NewProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

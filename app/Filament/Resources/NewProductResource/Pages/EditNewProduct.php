<?php

namespace App\Filament\Resources\NewProductResource\Pages;

use App\Filament\Resources\NewProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNewProduct extends EditRecord
{
    protected static string $resource = NewProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

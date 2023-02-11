<?php

namespace App\Filament\Resources\ProductAttributeResource\Pages;

use App\Filament\Resources\ProductAttributeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductAttribute extends EditRecord
{
    protected static string $resource = ProductAttributeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

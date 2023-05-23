<?php

namespace App\Filament\Resources\TagsResource\Pages;

use App\Filament\Resources\TagsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

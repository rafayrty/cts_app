<?php

namespace App\Filament\Resources\FormSubmissionsResource\Pages;

use App\Filament\Resources\FormSubmissionsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFormSubmissions extends ListRecords
{
    protected static string $resource = FormSubmissionsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

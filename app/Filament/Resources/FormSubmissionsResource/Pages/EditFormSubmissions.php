<?php

namespace App\Filament\Resources\FormSubmissionsResource\Pages;

use App\Filament\Resources\FormSubmissionsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFormSubmissions extends EditRecord
{
    protected static string $resource = FormSubmissionsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Personalized Book'),
            Actions\Action::make('new_personalized_notebook')
                ->label('New Personalized NoteBook')
                ->url('/admin/new-products/create'),
        ];
    }
}

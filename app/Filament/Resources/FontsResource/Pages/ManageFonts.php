<?php

namespace App\Filament\Resources\FontsResource\Pages;

use App\Filament\Resources\FontsResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

class ManageFonts extends ManageRecords
{
    protected static string $resource = FontsResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->using(function (array $data): Model {
                    return static::getModel()::create($data);
                })->after(function () {
                    Http::get(URL::to('/personalization/fonts'));
                }),
        ];
    }
}

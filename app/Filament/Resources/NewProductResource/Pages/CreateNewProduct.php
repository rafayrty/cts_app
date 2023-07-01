<?php

namespace App\Filament\Resources\NewProductResource\Pages;

use App\Filament\Resources\NewProductResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNewProduct extends CreateRecord
{
    protected static string $resource = NewProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //$data['is_published'] = true;

        //Delete the autosaved data
        //$product = Product::where('slug', $data['slug'])->get()->first();
        //$document = Document::where('product_id', $product->id)->get()->first();
        //Document::findOrFail($document->id)->delete();
        //Product::findOrFail($product->id)->delete();

        return $data;
    }
}

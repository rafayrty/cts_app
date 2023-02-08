<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        //$product = static::getModel()::create(
        //[
        //'demo_name' => $data['demo_name'],
        //'name' => $data['name'],
        //'slug' => $data['slug'],
        //'category_id' => $data['category'],
        //'price' => $data['price'],
        //'description' => $data['description'],
        //'images' => $data['images'],
        //]
        //);
        //$this->halt();
        //return $product;
        //unset($data['pdf_info']);
        //unset($data['pdf_name']);
        return $data;
    }
    //protected function mutateFormDataBeforeCreate(array $data): array
    //{
        //return $data;
    //}

    protected function handleRecordCreation(array $data): Model
    {
        return static::getModel()::create($data);
    }
}

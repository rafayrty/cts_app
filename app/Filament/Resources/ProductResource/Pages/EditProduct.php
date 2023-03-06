<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

protected function getFormActions(): array
{
    $array = [
        'replace_name' => $this->data['replace_name'],
        'product_name' => $this->data['product_name'],
        'demo_name' => $this->data['demo_name'],
        'description' => $this->data['description'],
        'price' => $this->data['price'],
        'front_price' => $this->data['discount_percentage'] ? $this->data['price'] - ($this->data['price'] * $this->data['discount_percentage'] / 100) : $this->data['price'],
        'has_sale' => $this->data['discount_percentage'] ? true : false,
        'images' => json_encode(array_values($this->data['images'])),
    ];

    return [
        $this->getSaveFormAction(),
        Action::make('preview_product')
        ->url('https://frontend.basmti.com/product/preview?'.http_build_query($array), true),
        $this->getCancelFormAction(),
    ];
}
}

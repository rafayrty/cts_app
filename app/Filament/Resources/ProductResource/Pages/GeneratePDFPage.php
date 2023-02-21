<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\Page;

class GeneratePDFPage extends Page
{
    protected static string $resource = ProductResource::class;

    protected static ?string $title = 'Generate PDF For You Products';

    protected static string $view = 'filament.resources.product-resource.pages.generate-p-d-f-page';

    public $product;

    public function mount($id)
    {
        $this->product = Product::findOrFail($id);
    }
}

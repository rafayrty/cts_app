<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Resources\Pages\Page;

class GeneratePDFPage extends Page
{
    protected static string $resource = OrderResource::class;

    protected static ?string $title = 'Generate PDF For Order Items';

    protected static string $view = 'filament.resources.product-resource.pages.generate-p-d-f-order-page';

    public $items = [];

    public function mount($id)
    {
        //$order = Order::findOrFail($id);
        //$this->items = $order->items;
        return redirect()->route('order.download.pdf',$id);
    }
}

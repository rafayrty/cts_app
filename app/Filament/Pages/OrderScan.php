<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class OrderScan extends Page
{
    //protected static string $resource = OrderResource::class;

    protected static string  $view = 'filament.resources.order-resource.pages.order-scan';

    protected static ?string $navigationGroup = 'Order Management';

    protected static ?string $slug = 'order_scan';

    public $barcode_number;

    protected $rules = [
        'barcode_number' => 'required',
    ];

    public function submit()
    {
        $this->validate();

        // Execution doesn't reach here if validation fails.
    }
}

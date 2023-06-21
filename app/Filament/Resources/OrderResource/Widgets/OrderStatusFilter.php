<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use Filament\Widgets\Widget;

class OrderStatusFilter extends Widget
{
    protected static string $view = 'filament.resources.order-resource.widgets.order-status-filter';

//    protected int | string | array $columnSpan = 'full';
protected int | string | array $columnSpan = [
    'md' => 2,
    'lg'=>3,
    'xl' => 3,
];
}

<?php

namespace App\Filament\Resources\Actions\Orders;

use App\Models\Covers;
use App\Models\Product;
use Closure;

class CalculationsOrder
{
    public function __invoke(Closure $set, Closure $get)
    {
        $sub_total = 0;
        $items = $get('../../order_items');
        foreach ($items as $item) {
            $cover_id = (int) $item['cover_id'];
            $product_id = (int) $item['product_id'];
            $product = Product::find($product_id);
            if($product){
                if ($cover_id != 0 && $product->product_type != 2) {
                    $sub_total += Covers::find($cover_id)->price;
                }
                if ($product_id != 0) {
                    $sub_total += Product::find($product_id)->price;
                }
            }
        }
        $set('../../sub_total', ceil($sub_total));
    }
}

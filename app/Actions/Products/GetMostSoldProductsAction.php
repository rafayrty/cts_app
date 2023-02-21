<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetMostSoldProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::orderBy('sold_amount', 'DESC')->skip(0)->take(4)->get();
    }
}

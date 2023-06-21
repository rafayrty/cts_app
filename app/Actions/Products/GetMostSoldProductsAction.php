<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetMostSoldProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::select(['id','images','product_name','slug','demo_name','replace_name','excerpt','price','discount_percentage'])
               ->orderBy('sold_amount', 'DESC')->skip(0)->take(4)->get();
    }
}

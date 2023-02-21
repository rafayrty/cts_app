<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetFeaturedProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::where('featured', 1)->skip(0)->take($limit)->get();
    }
}

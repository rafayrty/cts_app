<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetMostSoldProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::select(['id', 'images', 'product_type', 'product_name', 'slug', 'demo_name', 'replace_name', 'excerpt', 'price', 'discount_percentage'])
            ->where('product_type', 1)->orderBy('sold_amount', 'DESC')->where('is_published',1)->skip(0)->take(4)->get();
    }
}

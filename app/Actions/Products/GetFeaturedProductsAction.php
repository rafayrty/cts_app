<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetFeaturedProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::select(['id', 'images', 'product_name', 'slug', 'demo_name', 'replace_name', 'excerpt', 'price', 'discount_percentage'])
            ->where('featured', 1)->skip(0)->take($limit)->get();
    }
}

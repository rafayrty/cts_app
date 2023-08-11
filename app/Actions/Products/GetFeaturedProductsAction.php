<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetFeaturedProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::select(['id', 'images', 'product_type', 'product_name', 'slug', 'demo_name', 'replace_name', 'excerpt', 'price', 'discount_percentage'])
            ->where('product_type', 1)->where('featured', 1)->where('is_published',1)->orderBy('created_at', 'DESC')->skip(0)->take($limit)->get();
    }
}

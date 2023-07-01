<?php

namespace App\Actions\Products;

use App\Models\Product;

class GetPersonalizedNotebookProductsAction
{
    public function __invoke($limit = 4)
    {
        return Product::select(['id', 'images','languages', 'product_name', 'slug', 'demo_name', 'replace_name', 'excerpt', 'price', 'discount_percentage'])
            ->where('product_type', 2)->skip(0)->take($limit)->get();
    }
}

<?php

namespace App\Actions\Products;

use DB;
use App\Models\Product;

class GetPersonalizedNotebookProductsAction
{
    public function __invoke($limit = 4)
    {
        //return Product::select(['id', 'images', 'product_type', 'languages', 'product_name', 'slug', 'demo_name', 'replace_name', 'excerpt', 'price', 'discount_percentage'])
            //->where('product_type', 2)->where('featured',1)->where('is_published', 1)->orderBy('created_at','DESC')->skip(0)->take($limit)->get();

        return Product::select(['id', 'images', 'product_type', 'languages', 'product_name', 'slug', 'demo_name', 'replace_name', 'excerpt', 'price', 'discount_percentage'])
            ->where('product_type', 2)
            ->where('is_published', 1)
            ->orderBy(DB::raw('RAND()'))
            //->orderBy('created_at', 'DESC')
            //->inRandomOrder() // Add this line to randomize the result
            ->skip(0)
            ->take($limit)
            ->get();
    }
}

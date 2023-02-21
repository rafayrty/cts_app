<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\Request;

class GetProductsFilter
{
    public function __invoke(Request $request)
    {
        $products = Product::whereHas('category', function ($q) use ($request) {
            if ($request->category) {
                $q->whereIn('slug', explode(',', $request->category));
            }
        })
        ->whereHas('product_attributes', function ($q) use ($request) {
            if ($request->options) {
                $q->whereIn('slug', explode(',', $request->options));
            }
        });

        if ($request->min_price && $request->max_price) {
            $products->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        return $products->paginate(12);
    }
}

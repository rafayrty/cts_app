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

        if ($request->sort && $request->sort != '') {
            if ($request->sort == 'high-to-low') {
                $products->orderBy('price', 'DESC');
            }
            if ($request->sort == 'low-to-high') {
                $products->orderBy('price', 'ASC');
            }
            if ($request->sort == 'most-popular') {
                $products->orderBy('sold_amount', 'DESC');
            }
        }

        return $products->paginate(12);
    }
}

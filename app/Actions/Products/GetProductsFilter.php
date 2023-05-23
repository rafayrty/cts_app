<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\Request;

class GetProductsFilter
{
    public function __invoke(Request $request)
    {
        $products = Product::whereHas('categories', function ($q) use ($request) {
            if ($request->categories) {
                $q->whereIn('slug', explode(',', $request->categories));
            }
        })
        ->whereHas('product_attributes', function ($q) use ($request) {
            if ($request->options) {
                $q->whereIn('slug', explode(',', $request->options));
            }
        });

        if ($request->input('query') != '') {
            $products->where('product_name', 'like', '%'.$request->input('query').'%');
        }
        if ($request->min_price != '' && $request->max_price != '') {
            $products->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        //Check if published
        $products->where('is_published', 1);

        if ($request->sort != '') {
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

        $products->exclude(['pages', 'barcodes', 'dedications', 'pdf_info']);

        return $products->paginate($request->count ?? 6);
    }
}

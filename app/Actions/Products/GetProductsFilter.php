<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\Request;

class GetProductsFilter
{
    public function __invoke(Request $request)
    {
        $product_type = request()->product_type ?? 1;
        $products = Product::whereHas('categories', function ($q) use ($request) {
            if ($request->categories) {
                $categories = explode(',', $request->categories);
                if ($categories != null) {
                    $q->whereIn('slug', $categories);

                }
            }
        });

        if ($request->options) {
            $options = explode(',', $request->options);
            if ($options != null) {
                foreach ($options as $option) {
                    //if ($product_type == 2 && $option == 'Male' || $product_type == 2 && $option == 'Female') {
                    //} else {
                        $products->whereHas('product_attributes', function ($q) use ($option) {
                            $q->where('slug', $option);
                        });
                    //}
                }
            }
        }
        if ($request->input('query') != '') {
            $products->where('product_name', 'like', '%'.$request->input('query').'%');
        }
        if ($request->min_price != '' && $request->max_price != '') {
            $products->whereBetween('price', [$request->min_price, $request->max_price]);
        }

        //Check if published
        $products->where('is_published', 1)->where('product_type', $product_type);

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

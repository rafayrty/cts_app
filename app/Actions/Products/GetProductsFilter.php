<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Http\Request;

class GetProductsFilter
{
    public function __invoke(Request $request)
    {
        $products = Product::where('is_published', 1);

        if ($request->categories) {
                $categories = explode(",", $request->categories);
            if($categories != null){
                foreach($categories as $category){
                    Product::whereHas('categories', function ($q) use ($category) {
                        if ($category != null) {
                            $q->whereIn('slug', $category);

                        }
                    });
                }
            }
        }

        if($request->options){
            $options = explode(",", $request->options);
            if($options!=null){
                foreach($options as $option){
                    $products->whereHas('product_attributes', function ($q) use ($option) {
                        $q->where('slug', $option);
                    });
                }
            }
        }
        if ($request->input('query') != '') {
            $products->where('product_name', 'like', '%' . $request->input('query') . '%');
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

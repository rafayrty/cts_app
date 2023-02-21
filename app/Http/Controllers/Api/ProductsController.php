<?php

namespace App\Http\Controllers\Api;

use App\Actions\Products\GetFeaturedProductsAction;
use App\Actions\Products\GetMostSoldProductsAction;
use App\Actions\Products\GetProductAttributeOptionsAction;
use App\Actions\Products\GetProductsFilter;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function __construct(
    GetMostSoldProductsAction $getMostSoldProductsAction,
    GetFeaturedProductsAction $getFeaturedProductsAction,
    GetProductAttributeOptionsAction $getProductAttributeOptionsAction,
    GetProductsFilter $getProductsFilter
) {
        $this->getMostSoldProductsAction = $getMostSoldProductsAction;
        $this->getFeaturedProductsAction = $getFeaturedProductsAction;
        $this->getProductAttributeOptionsAction = $getProductAttributeOptionsAction;
        $this->getProductsFilter = $getProductsFilter;
    }

    public function get_product_slugs()
    {
        return Product::all()->pluck('slug');
    }

    public function get_products_filter(Request $request)
    {
        return ($this->getProductsFilter)($request);
    }

    public function get_product($slug)
    {
        return Product::where('slug', $slug)->get()->first();
    }

    public function get_product_covers($id)
    {
        $product = Product::find($id);
        if ($product) {
            return $product->covers;
        }

        return null;
    }

    public function get_most_sold_products()
    {
        return ($this->getMostSoldProductsAction)();
    }

    public function get_featured_products()
    {
        return ($this->getFeaturedProductsAction)();
    }

    public function get_product_attribute_options($id, $limit)
    {
        return ($this->getProductAttributeOptionsAction)($id, $limit);
    }
}

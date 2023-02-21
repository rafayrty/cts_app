<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;

class WishListController extends Controller
{
    public function add_wishlist($product_id)
    {
        $wishlist = Wishlist::create([
            'product_id' => $product_id,
            'user_id' => request()->user()->id,
        ]);

        return $wishlist;
    }

    public function remove_wishlist($wishlist_id)
    {
        $wishlist = Wishlist::create([
            'product_id' => $wishlist_id,
            'user_id' => request()->user()->id,
        ]);

        return $wishlist;
    }

    public function get_wishlist()
    {
        $wishlist = Wishlist::where('user_id', request()->user()->id)->with('product')->get();

        return $wishlist;
    }
}

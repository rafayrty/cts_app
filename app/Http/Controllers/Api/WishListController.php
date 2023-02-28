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

    public function remove_wishlist($product_id)
    {
        $wishlist = Wishlist::where('product_id', $product_id)->where('user_id', request()->user()->id)->get()->first();
        if (! $wishlist) {
            abort(404);
        }
        Wishlist::findOrFail($wishlist->id)->delete();

        return $wishlist;
    }

    public function get_wishlist()
    {
        $wishlist = Wishlist::where('user_id', request()->user()->id)->with('product')->get();

        return $wishlist;
    }

    public function check_in_wishlist($product_id)
    {
        if (! request()->user()) {
            return false;
        }
        $wishlist = Wishlist::where('user_id', request()->user()->id)->where('product_id', $product_id)->get()->count();

        if ($wishlist > 0) {
            return true;
        }

        return false;
    }
}

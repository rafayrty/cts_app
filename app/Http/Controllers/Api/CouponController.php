<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $coupon = Coupon::where('coupon_name', $request->name)->where('expiry', '>', now())->get()->first();

        if (! $coupon) {
            abort(404);
        }

        return $coupon;
    }
}

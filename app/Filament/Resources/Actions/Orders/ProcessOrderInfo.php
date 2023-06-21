<?php

namespace App\Filament\Resources\Actions\Orders;

use App\Models\Coupon;
use App\Models\Covers;
use App\Models\Product;
use App\Settings\GeneralSettings;
use Closure;
use Illuminate\Support\HtmlString;

class ProcessOrderInfo
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
                $sub_total = 0;
                $items = $get('order_items');
        if ($items) {
                foreach ($items as $item) {
                    $cover_id = (int) $item['cover_id'];
                    $product_id = (int) $item['product_id'];
            if ($cover_id != 0) {
                    $sub_total += Covers::find($cover_id)->price;
            }
            if ($product_id != 0) {
                    $sub_total += Product::find($product_id)->price;
            }
                }
                $set('sub_total', ceil($sub_total));

        }
                        $discount = 0;
                        $percentage = 0;
                        $sub_total = $get('sub_total');
                        if ($get('coupon')) {

                            $coupon_details = Coupon::find($get('coupon'));
                            if ($coupon_details) {
                                if ($sub_total < $coupon_details->min_amount) {
                                    echo "<script>alert('Amount must be greater than ".$coupon_details->min_amount."')</script>";
                                    $set('coupon', '');
                                } else {
                                    $discount = ceil($sub_total * ($coupon_details->discount_percentage / 100));
                                    $percentage = $coupon_details->discount_percentage;
                                }
                            }
                        }

                        return new HtmlString('<h1 style="font-weight:600">Subtotal: <strong style="font-weight:400">₪ '.$get('sub_total').'</strong></h1>
        <h1 style="font-weight:600">Shipping: <strong style="font-weight:400">₪ '.app(GeneralSettings::class)->shipping_fee.'</strong></h1>
        <h1 style="font-weight:600">Discount: <strong style="font-weight:400">₪ '.$discount.'    <span style="font-size:.75rem;color:red;">%'.$percentage.'</span></strong></h1>
        <h1 style="font-weight:600">Total: <strong style="font-weight:400">₪ '.($get('sub_total') + app(GeneralSettings::class)->shipping_fee) - $discount.'</strong></h1>
                            ');
    }
}

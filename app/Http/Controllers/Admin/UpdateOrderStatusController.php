<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancel;
use App\Mail\OrderUpdate;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class UpdateOrderStatusController extends Controller
{

    public function update_print_status($order_id,$status){

        if(auth()->user()->hasPermissionTo('orders.update', 'filament')){

            $order = Order::findOrFail($order_id);

            $order->print_house_status = $status;
            $order->save();
            return redirect()->back()->with('success','Update Successfully');
        }
        abort(401);
    }

    public function update_client_status($order_id,$status){

        if(auth()->user()->hasPermissionTo('orders.update', 'filament')){

            $order = Order::findOrFail($order_id);
            $order->client_status = $status;
            $order->save();

            if ($order->client_status->value != $status) {
                if ($status == 'packaging') {
                    $title = 'طلبك قيد '.$order->order_numeric_id.' التغليف';
                    $text = 'يسعدنا إخبارك بأنه تمت معالجة طلبك ونحن الآن في طور التعبئة والتغليف. سيتم إرسال طلبك في أقرب وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }
                if ($status == 'ready for delivery') {
                    $title = 'طلبك جاهز '.$order->order_numeric_id.' للشحن';
                    $text = 'سعداء جداً لإعلامك بأن طلبك قد تم إنهائه وهو جاهز الآن للتوصيل. سيتم إرسال بريد إلكتروني يحتوي على معلومات الشحن الخاصة بطلبك';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'cancel') {
                    Mail::to($order->user->email)->queue(new OrderCancel($order));
                }
            }

            return redirect()->back()->with('success','Update Successfully');
        }
        abort(401);
    }
}


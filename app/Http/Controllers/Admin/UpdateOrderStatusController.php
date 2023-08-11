<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\OrderCancel;
use App\Mail\OrderUpdate;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

class UpdateOrderStatusController extends Controller
{
    public function update_print_status($order_id, $status)
    {

        if (auth()->user()->hasPermissionTo('orders.update', 'filament')) {

            $order = Order::findOrFail($order_id);

            $order->print_house_status = $status;
            $order->save();

            return redirect()->back()->with('success', 'Update Successfully');
        }
        abort(401);
    }

    public function update_client_status($order_id, $status)
    {

        if (auth()->user()->hasPermissionTo('orders.update', 'filament')) {

            $order = Order::findOrFail($order_id);
            $original_client_status = $order->client_status;
            $order->client_status = $status;
            $order->save();

            if ($original_client_status != $status) {
                if ($status == 'packaging') {
                    $title = 'طلبك قيد '.$order->order_numeric_id.' التغليف';
                    $text = 'يسعدنا إخبارك بأنه تمت معالجة طلبك ونحن الآن في طور التعبئة والتغليف. سيتم إرسال طلبك في أقرب وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'new order') {
                    $title = 'طلب جديد '.$order->order_numeric_id.'';
                    $text = 'تم تسجيل طلبك بنجاح. شكرًا لاختياركم منتجاتنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'starting printing') {
                    $title = ' بدء عملية الطباعة لطلبك رقم'.$order->order_numeric_id.' ';
                    $text = 'لقد تم البدء في عملية طباعة طلبك. سنتأكد من جودة المنتجات قبل التوصيل.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'in delivery') {
                    $title = ' جاري التوصيل لطلبك رقم'.$order->order_numeric_id.' ';
                    $text = 'نحن بصدد توصيل طلبك الآن. يرجى الاستعداد لاستلامه.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'ready for delivery') {
                    $title = 'طلبك جاهز '.$order->order_numeric_id.' للشحن';
                    $text = 'سعداء جداً لإعلامك بأن طلبك قد تم إنهائه وهو جاهز الآن للتوصيل. سيتم إرسال بريد إلكتروني يحتوي على معلومات الشحن الخاصة بطلبك';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'cancel') {
                    $title = ' إلغاء الطلب رقم'.$order->order_numeric_id.' ';
                    $text = 'نأسف لإبلاغكم أن الطلب قد تم إلغاؤه. في حال كانت هناك أي مشكلة، يرجى التواصل معنا.';
                    Mail::to($order->user->email)->queue(new OrderCancel($order));
                }

                if ($status == 'stuck') {
                    $title = ' الطلب رقم'.$order->order_numeric_id.'متوقف';
                    $text = 'نعتذر، لكن هناك تأخير في تقديم طلبك. سنقوم بمعالجة المشكلة بأسرع وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order));
                }

                if ($status == 'done') {
                    $title = ' الطلب رقم'.$order->order_numeric_id.'تمّ';
                    $text = 'نفيدكم بأن الطلب قد تم تنفيذه بنجاح. شكرًا لاختياركم منتجاتنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order));
                }
            }

            return redirect()->back()->with('success', 'Update Successfully');
        }
        abort(401);
    }
}

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

            if($order->print_house_status == 'starting printing'){
                    $this->update_client_status($order_id,'starting printing');
            }elseif($order->print_house_status == 'packaging'){
                    $this->update_client_status($order_id,'packaging');
            }

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
                    $title = 'طلبك قيد '.$order->order_numeric_id.'<br/><span style="color:#00214D"> التغليف</span>';
                    $text = 'يسعدنا إخبارك بأنه تمت معالجة طلبك ونحن الآن في طور التعبئة والتغليف. سيتم إرسال طلبك في أقرب وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'new order') {
                    $title = 'طلب جديد '.$order->order_numeric_id.'';
                    $text = 'تم تسجيل طلبك بنجاح. شكرًا لاختياركم منتجاتنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'starting printing') {
                    $title = 'طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">طلبك قيد الطباعة</span>';
                    $text = 'نعلمك اننا بدأنا في تحضير طلبكم - انتظروا منا رسالة جديدة تعلمكم ان الطلب سيكون جاهز خلال الايام القريبة';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'in delivery') {
                    $title = ' جاري التوصيل لطلبك رقم'.$order->order_numeric_id.' ';
                    $text = 'نحن بصدد توصيل طلبك الآن. يرجى الاستعداد لاستلامه.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'ready for delivery') {
                    $title = 'طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D"> جاهز للشحن</span>';
                    $text = 'نحن سعداء باعلامك بان طلبك تم تجهيزه ونحن بصدد تسليمه لشركة الارساليات ستصلك رسالة نصية SMS من شركة الارساليات تعلمك بوصول الطرد.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'cancel') {
                    $title = ' إلغاء الطلب رقم'.$order->order_numeric_id.' ';
                    $text = 'نأسف لإبلاغكم أن الطلب قد تم إلغاؤه. في حال كانت هناك أي مشكلة، يرجى التواصل معنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order,$title,$text));
                }

                if ($status == 'stuck') {
                    $title = ' الطلب رقم'.$order->order_numeric_id.'متوقف';
                    $text = 'نعتذر، لكن هناك تأخير في تقديم طلبك. سنقوم بمعالجة المشكلة بأسرع وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order,$title,$text));
                }

                if ($status == 'done') {
                    $title = ' الطلب رقم'.$order->order_numeric_id.'<br/><span style="color:#00214D"> تم توصيل الطلب بنجاح</span>';
                    $text = 'نفيدكم بأن الطلب قد تم تنفيذه بنجاح. شكرًا لاختياركم منتجاتنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order,$title,$text));
                }
            }

            return redirect()->back()->with('success', 'Update Successfully');
        }
        abort(401);
    }
}

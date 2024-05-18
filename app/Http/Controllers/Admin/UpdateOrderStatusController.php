<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

            if ($order->print_house_status->value == 'starting printing') {
                $this->update_client_status($order_id, 'starting printing');
            } elseif ($order->print_house_status->value == 'packaging') {
                $this->update_client_status($order_id, 'packaging');
            }

            $order->save();

            return redirect()->back()->with('success', 'Update Successfully');
            //return redirect(url()->previous().'#'.$scroll_id);
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

                if ($status == 'new order') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">تم الطلب بنجاح</span>';
                    $text = 'شكرًا  لاختيارك بصمتي،
قمت بما يجب وعلينا نحن التنفيذ، طلبك سيدخل الان مرحلة الانشاء ، لا داع للقلق في كل مرحلة ستصلك رسالة منا لحتلنتك بوضع طلبك ومرحلته.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'starting printing') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">طلبك قيد الطباعة </span>';
                    $text = 'يسعدنا إخبارك بأنه تم معالجة طلبك ونحن الآن في مرحلة الطباعة. سيتم إرسال طلبك في أقرب وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'packaging') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">طلبك قيد التغليف</span>';
                    $text = 'يسعدنا إخبارك بأنه تم معالجة طلبك ونحن الآن في مرحلة التغليف. سيتم إرسال طلبك في أقرب وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'ready for delivery') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">جاهز للشحن</span>';
                    $text = 'نحن سعداء باعلامك بان طلبك تم تجهيزه ونحن بصدد تسليمه لشركة الارساليات
ستصلك رسالة نصية SMS من شركة الارساليات تعلمك بوصول الطرد.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'in delivery') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">قيد الشحن </span>';
                    $text = 'نحن سعداء باعلامك بان طلبك قد تم تسليمه الى شركة الارساليات ستصلك رسالة SMS من شركة الارساليات لتعلمك بوصول الطرد';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'done') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">تم توصيل الطلب بنجاح </span>';
                    $text = 'سعداء بأن المنتج بين يديكم ، نرجو أن يكون قد نال اعجابكم، لطفا منكم ان تشاركوا المنتج في مواقع التواصل الاجتماعي';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'stuck') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">متوقف</span>';
                    $text = 'نعتذر، لكن هناك تأخير في تقدم طلبك. سنقوم بمعالجة المشكلة بأسرع وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($status == 'cancel') {
                    $title = ' طلبك رقم '.$order->order_numeric_id.'<br/><span style="color:#00214D">تم إلغاء الطلب </span>';
                    $text = 'نأسف لإبلاغكم أن الطلب قد تم إلغاؤه. في حال كانت هناك أي مشكلة، يرجى التواصل معنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }
            }

            return redirect()->back()->with('success', 'Update Successfully');
            //return redirect(url()->previous().'#'.$scroll_id);
        }
        abort(401);
    }
}

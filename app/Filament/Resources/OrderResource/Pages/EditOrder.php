<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Mail\OrderCancel;
use App\Mail\OrderUpdate;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Mail;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getActions(): array
    {
        return [
            //Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->client_status->value != $data['client_status']) {
                if ($data['client_status'] == 'packaging') {
                    $title = 'طلبك قيد '.$order->order_numeric_id.' التغليف';
                    $text = 'يسعدنا إخبارك بأنه تمت معالجة طلبك ونحن الآن في طور التعبئة والتغليف. سيتم إرسال طلبك في أقرب وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($data['client_status'] == 'new order') {
                    $title = 'طلب جديد '.$order->order_numeric_id.'';
                    $text = 'تم تسجيل طلبك بنجاح. شكرًا لاختياركم منتجاتنا.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($data['client_status'] == 'starting printing') {
                    $title = ' بدء عملية الطباعة لطلبك رقم'.$order->order_numeric_id.' ';
                    $text = 'لقد تم البدء في عملية طباعة طلبك. سنتأكد من جودة المنتجات قبل التوصيل.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($data['client_status'] == 'in delivery') {
                    $title = ' جاري التوصيل لطلبك رقم'.$order->order_numeric_id.' ';
                    $text = 'نحن بصدد توصيل طلبك الآن. يرجى الاستعداد لاستلامه.';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($data['client_status'] == 'ready for delivery') {
                    $title = 'طلبك جاهز '.$order->order_numeric_id.' للشحن';
                    $text = 'سعداء جداً لإعلامك بأن طلبك قد تم إنهائه وهو جاهز الآن للتوصيل. سيتم إرسال بريد إلكتروني يحتوي على معلومات الشحن الخاصة بطلبك';
                    Mail::to($order->user->email)->queue(new OrderUpdate($order, $title, $text));
                }

                if ($data['client_status'] == 'cancel') {
                    $title = ' إلغاء الطلب رقم'.$order->order_numeric_id.' ';
                    $text = 'نأسف لإبلاغكم أن الطلب قد تم إلغاؤه. في حال كانت هناك أي مشكلة، يرجى التواصل معنا.';
                    Mail::to($order->user->email)->queue(new OrderCancel($order));
                }

                if ($data['client_status'] == 'stuck') {
                    $title = ' الطلب رقم'.$order->order_numeric_id.'متوقف';
                    $text = 'نعتذر، لكن هناك تأخير في تقديم طلبك. سنقوم بمعالجة المشكلة بأسرع وقت ممكن.';
                    Mail::to($order->user->email)->queue(new OrderCancel($order));
                }

                if ($data['client_status'] == 'done') {
                    $title = ' الطلب رقم'.$order->order_numeric_id.'تمّ';
                    $text = 'نفيدكم بأن الطلب قد تم تنفيذه بنجاح. شكرًا لاختياركم منتجاتنا.';
                    Mail::to($order->user->email)->queue(new OrderCancel($order));
                }
        }

        return $data;
    }
}

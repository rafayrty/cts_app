<?php

namespace App\Actions\Orders;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class GenerateInvoice
{
    public function __invoke(Order $order)
    {

        $email = config('mail.from.address');
        $items = [];

        foreach($order->items as $item){
            if($item->product_type==1){
                $items[] = ['description'=>$item->name. " + " .$item->cover['name'],'unitprice_incvat'=>($item->total/100),'quantity'=>$item->quantity];
            }else{
                $items[] = ['description'=>$item->name,'unitprice_incvat'=>$item->price/100,'quantity'=>$item->quantity];
            }
        }
        $items[] = ['description'=>'דמי משלוח','unitprice_incvat'=>$order->shipping / 100,'quantity'=>1];

        $payment_info = json_decode($order->payment_info,true);
        $payment_info = [
            'sum'=>$order->total/100,
            'card_type'=>strtoupper($payment_info['payme_transaction_card_brand']),
            'card_number'=>substr($payment_info['buyer_card_mask'],-4),
            'exp_year'=>'20'.substr($payment_info['buyer_card_exp'],-2),// Get year
            'exp_month'=>substr($payment_info['buyer_card_exp'],0,2),// Get month
            'holder_id'=>$payment_info['buyer_social_id'],
            "holder_name" => $payment_info['buyer_name'],
            'confirmation_code'=>$payment_info['payme_transaction_auth_number']
        ];
        $response = Http::post('https://api.icount.co.il/api/v3.php/doc/create', [
            'cid' => 'topprint',
            'user' => 'basmti',
            'pass' => 'Toprint2023!',
            'doc_title'=>"Invoice For Order # ".$order->order_numeric_id,
            'hwc'=>"Invoice For Order # ".$order->order_numeric_id,
            'doctype' => 'invrec',
            'client_name' => $order->user->first_name. ' ' .$order->user->last_name,
            'client_email'=>$order->user->email,
            'phone' => $order->user->phone_number,
            'lang' => 'he',
            'currency_code' => 'ILS',
            'duedate' => $order->created_at,
            'items' => $items,
            'send_email' => 1,
            'discount_incvat'=>$order->discount_total / 100,
            'email_cc_me' => 1,
            //'tax_exempt'=>1,
            'vat_percent'=>17,
            "cc" => $payment_info,
            'email_cc' => $email,
        ]);

        if ($response->failed()) {
            Log::error($response->body());
            throw new RuntimeException('Failed to connect ', $response->status());
        }

        Log::info($response);
        return $response;
    }
}

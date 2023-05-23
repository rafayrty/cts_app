<?php

namespace App\Actions\Personalization;

use App\Models\Order;
use App\Models\OrderItem;

class SearchBarcodeWithOrderItemId
{
   /**
    * Preview PDF by sending the request to the PDFGenerator
    *
    * @param  int $order_item_id
    * @return string
    */
    public function __invoke($order_item_id):array
    {

       $barcode_found = null;
       $order_item = OrderItem::find($order_item_id);
       $order = Order::findOrFail($order_item->order_id);

       foreach ($order->barcodes as $bar) {
           $parts = explode('-', $bar['barcode_number']);
           $lastPart = end($parts);
           if ($lastPart == $order_item_id) {
               $barcode_found = $bar;
           }
       }
        return $barcode_found;
    }

}


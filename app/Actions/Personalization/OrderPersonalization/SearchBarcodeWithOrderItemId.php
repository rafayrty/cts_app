<?php

namespace App\Actions\Personalization;

use App\Models\Order;
use App\Models\OrderItem;

class SearchBarcodeWithOrderItemId
{
    /**
     * Preview PDF by sending the request to the PDFGenerator
     *
     * @param  int  $order_item_id,int $document_id
     * @return string
     */
    public function __invoke($order_item_id, $document_id)
    {

        $barcode_found = null;
        $order_item = OrderItem::find($order_item_id);
        $order = Order::findOrFail($order_item->order_id);

        foreach ($order->barcodes as $bar) {
            $parts = explode('-', $bar['barcode_number']);
            $lastPart = end($parts);
            $docPart = $parts[2];
            if ($lastPart == $order_item_id && $document_id == $docPart) {
                $barcode_found = $bar;
            }

        }

        return $barcode_found;
    }
}

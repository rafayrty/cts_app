<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Order;
use App\Models\OrderItem;

class PackagingManagementController extends Controller
{
    public function find_packaging_order($barcode)
    {

        if (!auth()->user()->hasPermissionTo('orders.update', 'filament')) {
            abort(401);
        }

        $order = Order::whereJsonContains('barcodes', ['barcode_number' => $barcode])->with('items')->get()->first();
        if (!$order) {
            abort(404, "No Order Found");
        }
        //Check Document type is actually cover

        $doc_id = $this->get_document_id_from_barcode($barcode);
        $document = Document::findOrFail($doc_id);
        if ($document->type != 0 && $document->type != 1) {
            abort(404, "The Barcode is Not For a Cover");
        }

        return ['order' => $order, 'order_item' => OrderItem::findOrFail($this->get_order_item_id_from_barcode($barcode))];
    }

    public function get_document_id_from_barcode($barcode)
    {
        $parts = explode('-', $barcode);

        // Get the second element (index 1) from the array
        $value = $parts[2];
        return $value;
    }

    public function get_order_item_id_from_barcode($barcode)
    {
        $parts = explode('-', $barcode);
        $lastPart = end($parts);
        return $lastPart;
    }
}

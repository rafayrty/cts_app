<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Order;
use App\Models\OrderItem;

class PrintingManagementController extends Controller
{
    public function find_book($barcode)
    {

        if (! auth()->user()->hasPermissionTo('orders.update', 'filament')) {
            abort(401);
        }

        $order = Order::whereJsonContains('barcodes', ['barcode_number' => $barcode])->get()->first();
        if (! $order) {
            abort(404, 'No Order Found');
        }

        //Check Document type is actually book

        $doc_id = $this->get_document_id_from_barcode($barcode);
        $document = Document::findOrFail($doc_id);
        if ($document->type != 2) {
            abort(404, 'The Barcode is Not For a Book');
        }

        //Get OrderItem
        $order_item_id = $this->get_order_item_id_from_barcode($barcode);

        return OrderItem::findOrFail($order_item_id);
    }

    public function find_cover($barcode, $book_item_id)
    {

        if (! auth()->user()->hasPermissionTo('orders.update', 'filament')) {
            abort(401);
        }

        $pattern = '/^\d+-\d+-\d+-\d+$/';
        if (! preg_match($pattern, $barcode)) {
            abort(422, 'The Barcode Entered is Incorrect try Rescanning');
        }

        $order = Order::whereJsonContains('barcodes', ['barcode_number' => $barcode])->get()->first();
        $doc_id = $this->get_document_id_from_barcode($barcode);
        if (! $order) {
            abort(404, 'No Order Found');
        }

        //Check Document type is actually cover
        $document = Document::findOrFail($doc_id);
        if ($document->type != 0 && $document->type != 1) {
            abort(404, 'The Barcode is Not For a Cover');
        }

        //Get OrderItem
        $order_item_id = $this->get_order_item_id_from_barcode($barcode);
        $order_item = OrderItem::findOrFail($order_item_id);

        //Verify if the Cover belons to the same book
        if ($book_item_id == $order_item_id) {
            return $order_item->cover;
        }
        abort(404, 'Cover Not Found');
    }

    public function get_document_id_from_barcode($barcode)
    {
        $parts = explode('-', $barcode);
        // Get the second element (index 1) from the array
        $value = $parts[2];

        return $value;
    }

    public function get_product_id_from_barcode($barcode)
    {
        $parts = explode('-', $barcode);

        // Get the second element (index 1) from the array
        $value = $parts[1];

        return $value;
    }

    public function get_order_item_id_from_barcode($barcode)
    {
        $parts = explode('-', $barcode);
        $lastPart = end($parts);

        return $lastPart;
    }
}

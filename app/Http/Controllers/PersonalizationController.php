<?php

namespace App\Http\Controllers;

use App\Actions\GeneratePDF;
use App\Actions\GeneratePDFOrder;
use App\Models\Document;
use App\Models\Fonts;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonalizationController extends Controller
{
    public function __construct(GeneratePDF $generatePDF, GeneratePDFOrder $generatePDFOrder)
    {
        $this->GeneratePDFAction = $generatePDF;
        $this->GeneratePDFOrderAction = $generatePDFOrder;
    }

   public function generatePDFFromDocument($id)
   {
       $document = Document::findOrFail($id);
       $pages = $document->product->pagesParsed;
       $dedications = $document->product->dedicationsParsed;
       $replace_name = $document->product->replace_name;
       //Get pages for where the document is the same
       $found = null;
       $pages_array = [];
       foreach ($pages as  &$page) {
           foreach ($page['pages']['predefined_texts'] as &$page_data) {
               $page_data['text'] = trim(str_replace('&nbsp;', ' ', str_replace('{basmti}', $replace_name, $page_data['text'])));
           }
           if ($page['document'] == $document->name) {
               $pages_array[] = ['page' => $page['page'], 'predefined_texts' => $page['pages']['predefined_texts']];
           }
       }
       $dedications_array = [];
       foreach ($dedications as  &$dedication) {
           foreach ($dedication['dedications']['dedication_texts'] as &$dedication_data) {
               $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
           }
           if ($dedication['document'] == $document->name) {
               $dedications_array[] = ['page' => $dedication['page'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
           }
       }

       $file = Storage::disk('local')->path('public/'.$document->attatchment);
       $fonts = Fonts::all();
       $array = [];
       if ($fonts) {
           foreach ($fonts as $font) {
               $array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment)];
           }
       }
       $array[] = ['font_name' => 'GE-Dinar-Medium', 'attatchment' => public_path('fonts/GE-Dinar-One-Medium.ttf')];

       if (file_exists($file)) {
           $response = ($this->GeneratePDFAction)($file, $pages_array, $dedications_array, array_values($array));
           $body = $response->getBody();
           // Explicitly cast the body to a string
           $stringBody = (string) $body;

           $file_name = 'GeneratedPDF-'.time().'.pdf';
           $path = 'downloads/'.$file_name;
           Storage::put($path, $stringBody);

           return response()->download(Storage::path($path), $file_name, [], 'inline');
       }
   }

   public function generatePDFFromDocumentOrder($id, $order_item_id)
   {
       $document = Document::findOrFail($id);
       $pages = $document->product->pagesParsed;
       $dedications = $document->product->dedicationsParsed;
       $replace_name = $document->product->replace_name;
       $barcodes = $document->product->barcodes;
       //Get pages for where the document is the same
       $found = null;
       $pages_array = [];
       $barcodes_array = [];
       $order_item = OrderItem::find($order_item_id)->order_id;
       $order = Order::findOrFail($order_item);
       //Search for the correct barcode
       $barcode_found = null;
       foreach ($order->barcodes as $bar) {
           $parts = explode('-', $bar['barcode_number']);
           $lastPart = end($parts);
           if ($lastPart == $id) {
               $barcode_found = $bar;
           }
       }
       foreach ($barcodes as  &$barcode) {
           if ($barcode['document'] == $document->name) {
               $barcode_array[] = ['barcode_path' => Storage::path('public/'.$barcode_found['barcode_path']), 'page' => $barcode['page'], 'barcode_info' => $barcode['barcodes']['barcodes'][0]];
           }
       }

       foreach ($pages as  &$page) {
           foreach ($page['pages']['predefined_texts'] as &$page_data) {
               $page_data['text'] = trim(str_replace('&nbsp;', ' ', str_replace('{basmti}', $replace_name, $page_data['text'])));
           }
           if ($page['document'] == $document->name) {
               $pages_array[] = ['page' => $page['page'], 'predefined_texts' => $page['pages']['predefined_texts']];
           }
       }
       $dedications_array = [];
       foreach ($dedications as  &$dedication) {
           foreach ($dedication['dedications']['dedication_texts'] as &$dedication_data) {
               $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
           }
           if ($dedication['document'] == $document->name) {
               $dedications_array[] = ['page' => $dedication['page'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
           }
       }

       $file = Storage::disk('local')->path('public/'.$document->attatchment);
       $fonts = Fonts::all();
       $array = [];
       if ($fonts) {
           foreach ($fonts as $font) {
               $array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment)];
           }
       }
       $array[] = ['font_name' => 'GE-Dinar-Medium', 'attatchment' => public_path('fonts/GE-Dinar-One-Medium.ttf')];

       if (file_exists($file)) {
           $response = ($this->GeneratePDFOrderAction)($file, $pages_array, $dedications_array, $barcode_array, array_values($array));
           $body = $response->getBody();
           // Explicitly cast the body to a string
           $stringBody = (string) $body;

           $file_name = 'GeneratedPDF-'.time().'.pdf';
           $path = 'downloads/'.$file_name;
           Storage::put($path, $stringBody);

           return response()->download(Storage::path($path), $file_name, [], 'inline');
       }
   }

   /**
    * Generate PDF by sending the request to the
    *
    * @param  \Illuminate\Http\Request  $request
    * @return string|null
    */
   public function generatePDF($id)
   {
       $product = Product::findOrFail($id);
       $document = $product->documents->last();
       //$pages = $product->pages;
       $file = Storage::disk('local')->path('public/'.$document->attatchment);

       $product = Product::findOrFail($id);

       $document = $product->documents->first();
       $pages = $product->pages;

       $fonts = Fonts::all();
       $array = [];
       if ($fonts) {
           foreach ($fonts as $font) {
               $array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment)];
           }
       }

       $array[] = ['font_name' => 'Cairo-Semibold', 'attatchment' => public_path('fonts/Cairo-Semibold.ttf')];
       if (file_exists($file)) {
           $response = ($this->GeneratePDFAction)($file, $pages, array_values($array));
           $body = $response->getBody();
           // Explicitly cast the body to a string
           $stringBody = (string) $body;

           $file_name = 'GeneratedPDF-'.time().'.pdf';
           $path = 'downloads/'.$file_name;
           Storage::put($path, $stringBody);

           return response()->download(Storage::path($path), $file_name, [], 'inline');
       }

       abort(404);
   }
}

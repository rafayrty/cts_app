<?php

namespace App\Http\Controllers;

use App\Actions\GeneratePDF;
use App\Actions\GeneratePDFOrder;
use App\Actions\GeneratePDFOrderZip;
use App\Actions\Personalization\CreateZipFileFromList;
use App\Actions\Personalization\GetDocumentInformation;
use App\Actions\Personalization\GetOrderItemInputs;
use App\Actions\Personalization\SearchBarcodeWithOrderItemId;
use App\Models\Document;
use App\Models\Fonts;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class PersonalizationController extends Controller
{
    public function __construct(GeneratePDF $generatePDF, GeneratePDFOrder $generatePDFOrder,
        GeneratePDFOrderZip $generatePDFOrderZip, GetDocumentInformation $getDocumentInformation, GetOrderItemInputs $getOrderItemInputs,
        SearchBarcodeWithOrderItemId $searcBarcodeWithOrderItemId, CreateZipFileFromList $createZipFileFromList
    ) {
        $this->GeneratePDFAction = $generatePDF;
        $this->GeneratePDFOrderAction = $generatePDFOrder;
        $this->GeneratePDFOrderZipAction = $generatePDFOrderZip;
        $this->GetDocumentInformation = $getDocumentInformation;
        $this->GetOrderItemInputs = $getOrderItemInputs;
        $this->SearchBarcodeWithOrderItemId = $searcBarcodeWithOrderItemId;
        $this->CreateZipFileFromList = $createZipFileFromList;
    }

    public function update_font($id,$order_id){

        $this->validate(request(),[
            'font'=>'required'
        ]);

        $product = Product::findOrFail($id);
        $order = Order::findOrFail($order_id);

        if(request()->font != 'GE-Dinar-Medium'){

            $font = Fonts::where('font_name',request()->font)->get()->first();

            if(!$font) abort(404);

            $selected_font = $font->font_name;
        }else{
            $selected_font = request()->font;
        }
        $pages = $product->pages;

        foreach($pages as &$page){
            foreach($page['pages']['predefined_texts'] as &$text){
                    $text['font_face'] = $selected_font;
            }
        }
        $product->update(['pages'=>$pages]);
        //Delete the zip file
        if($order->zip_path){
            if(file_exists($order->zip_path)){
                unlink($order->zip_path);
            }
        }
        $order->update(['queue_status'=>null,'zip_path'=>null]);
        return redirect()->back()->with('success',"Font Update Successfully");
    }

    public function generatePDFFromDocument($id)
    {
        $document = Document::findOrFail($id);

        $pages = $document->product->pagesParsed;
        $dedications = $document->product->dedicationsParsed;
        $barcodes = $document->product->barcodes;

        $inputs = ['name' => $document->product->replace_name, 'first_name' => split_name($document->product->replace_name)[0], 'age' => '٨', 'init' => $document->product->replace_name[0]];
        $replace_name = $inputs['name'];
        $replace_first_name = $inputs['first_name'];
        $replace_age = $inputs['age'];
        $replace_init = $inputs['init'];
        //Get pages for where the document is the same
        $found = null;
        $pages_array = [];

        foreach ($pages as &$page) {
            foreach ($page['pages']['predefined_texts'] as &$page_data) {
                $page_data['text'] = str_replace('{basmti}', $replace_name, $page_data['text']);
                $page_data['text'] = str_replace('{age}', $replace_age, $page_data['text']);

                $page_data['text'] = str_replace('{init}', $replace_init, $page_data['text']);
            }
            if ($page['document'] == $document->pdf_name) {
                $pages_array[] = ['page' => $page['page'], 'predefined_texts' => $page['pages']['predefined_texts']];
            }
        }
        $dedications_array = [];
        foreach ($dedications as &$dedication) {
            foreach ($dedication['dedications']['dedication_texts'] as &$dedication_data) {
                $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
                $dedication_data['text'] = str_replace('{age}', $replace_age, $dedication_data['text']);
                $dedication_data['text'] = str_replace('{init}', $replace_init, $dedication_data['text']);
            }
            if ($dedication['document'] == $document->pdf_name) {
                $dedications_array[] = ['page' => $dedication['page'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
            }
        }

        $barcode_array = [];
        if($barcodes){

            foreach ($barcodes as &$barcode) {
                if ($barcode['document'] == $document->pdf_name) {
                    $barcode_array[] = ['barcode_path' => Storage::path('public/download.png'), 'page' => $barcode['page'], 'barcode_info' => $barcode['barcodes']['barcodes'][0]];
                }
            }

        }
        $file = Storage::disk('local')->path('public/'.$document->attatchment);
        $fonts = Fonts::all();
        $array = [];
        if ($fonts) {
            foreach ($fonts as $font) {
                $array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment), 'subsetting' => $font->subsetting];
            }
        }
        $array[] = ['font_name' => 'GE-Dinar-Medium', 'attatchment' => public_path('fonts/GE-Dinar-One-Medium.ttf')];

        if (file_exists($file)) {
            $response = ($this->GeneratePDFAction)($file, $pages_array, $dedications_array,$barcode_array, array_values($array));
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
     * Downlaod All PDFs by Generating a ZipFile
     *
     * @param  int  $order_id
     * @return Illuminate\Http\Response
     */
    public function pdf_download_all_document_order($order_id)
    {
        $order = Order::findOrFail($order_id);

        if ($order->zip_path != null && file_exists($order->zip_path)) {

            // Create a BinaryFileResponse for the zip file
            //$response = new BinaryFileResponse($order->zip_path);

            // Delete the zip file after it has been sent
            $zipName = 'Order-'.Order::findOrFail($order_id)->order_numeric_id.'.zip';
            $file = $order->zip_path;
            // Set the appropriate headers for the response
            //$response->headers->set('Content-Disposition', 'attachment; filename="'.$zipName.'.zip"');
            // Set the appropriate headers for the download
            $headers = [
                'Content-Type' => 'application/zip',
                'Content-Disposition' => 'attachment; filename="'.$zipName.'"',
            ];

            return response()->file($file, $headers);
        }
        //Set zip_path to null
        Order::findOrFail($order_id)->update(['zip_path' => null]);
        $order_items = Order::findOrFail($order_id)->items;
        foreach ($order_items as $order_item) {

            $order_item_id = $order_item->id;
            foreach ($order_item->product->documents as $document) {
                //Checks if cover is the one selected by the user also check if it isn't a book
                if($order_item->product_type == 1){
                    if (! ($document->type == ($order_item->cover['type'] == 2 ? 0 : 1) || $document->type == 2)) {
                        continue;
                    }
                }else{
                    //Check if cover is the one select by the user
                    if($order_item->language == 'english'){
                        //if($document->language == 'english' && $document->type == 0){
                            //continue;
                        //}
                        if($document->language !='english'){
                            continue;
                        }
                    }else{
                        //if($document->type == 0 && $document->language != 'english'){
                            //continue;
                        //}
                        if($document->language == 'english'){
                            continue;
                        }
                    }
                    if($document->type != 0){
                        continue;
                    }
                    //if($order_item->language == 'english'){
                        //if($document->type != 0 && $order_item->language == 'english'){
                            //continue;
                        //}
                    //}else{
                            //if ($document->type != 0) {
                                //continue;
                            //}
                    //}

                }
                $data = ($this->GetDocumentInformation)($document->id);
                $document = $data->document;
                $product = $document->product;
                $pages = $data->pages;
                $dedications = $data->dedications;
                $barcodes = $data->barcodes;

                $replace_name = $product->replace_name;
                $inputs = ($this->GetOrderItemInputs)($order_item_id, $replace_name);

                //Get pages for where the document is the same
                $found = null;
                $pages_array = [];
                $barcode_array = [];

                //Search for the correct barcode
                $barcode_found = ($this->SearchBarcodeWithOrderItemId)($order_item_id, $document->id);
                if ($barcode_found == null) {
                    abort(404);
                }

                $replace_name = $inputs['name'];
                $replace_age = $inputs['age'];
                $replace_init = $inputs['init'];

                foreach ($barcodes as &$barcode) {
                    if ($barcode['document'] == $document->pdf_name) {
                        $barcode_array[] = ['barcode_path' => Storage::path('public/'.$barcode_found['barcode_path']), 'page' => $barcode['page'], 'barcode_info' => $barcode['barcodes']['barcodes'][0]];
                    }
                }
                foreach ($pages as &$page) {
                    foreach ($page['pages']['predefined_texts'] as &$page_data) {
                        $page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
                        $page_data['text'] = str_replace('{age}', $replace_age, $page_data['text']);
                        $page_data['text'] = str_replace('{init}', $replace_init, $page_data['text']);
                    }
                    if ($page['document'] == $document->pdf_name) {
                        $pages_array[] = ['page' => $page['page'], 'predefined_texts' => $page['pages']['predefined_texts']];
                    }
                }
                $dedications_array = [];

                $dedication_text = OrderItem::findOrFail($order_item_id)->dedication;
                foreach ($dedications as &$dedication) {
                    foreach ($dedication['dedications']['dedication_texts'] as &$dedication_data) {
                        $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_text));
                        $dedication_data['text'] = str_replace('{age}', $replace_age, $dedication_text);
                        $dedication_data['text'] = str_replace('{init}', $replace_init, $dedication_text);
                    }
                    if ($dedication['document'] == $document->pdf_name) {
                        $dedications_array[] = ['page' => $dedication['page'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
                    }
                }

                $file = Storage::disk('local')->path('public/'.$document->attatchment);
                $fonts = Fonts::all();
                $array = [];
                if ($fonts) {
                    foreach ($fonts as $font) {
                        $array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment), 'subsetting' => $font->subsetting];
                    }
                }
                $array[] = ['font_name' => 'GE-Dinar-Medium', 'attatchment' => public_path('fonts/GE-Dinar-One-Medium.ttf'), 'subsetting' => 0];
                if (file_exists($file)) {

                    if($order_item->product_type == 1){
                        if ($document->type == '1') {
                            $type = 'Hard Cover';
                        } elseif ($document->type == '2') {
                            $type = 'Book';
                        } else {
                            $type = 'Soft Cover';
                        }
                    }else{
                        $type = 'Cover '. $document->language == 'english' ? "English" : "";
                    }
                    $file_name = $order_id.'-'.($order_item->product_id ?? 1234).'-'.$document->id.'-'.$order_item_id.' '.$type.'.pdf';
                    $files[] = [
                        'file_name' => $file_name,
                        'file' => $file,
                        'pages' => $pages_array,
                        'dedications' => $dedications_array,
                        'barcodes' => $barcode_array,
                        'fonts' => array_values($array),
                    ];
                    //$response = ($this->GeneratePDFOrderAction)($file, $pages_array, $dedications_array, $barcode_array, array_values($array));
                    //$body = $response->getBody();
                    // Explicitly cast the body to a string
                    //$stringBody = (string) $body;

                    //$type = 'Soft Cover';
                    //if ($document->type == '1') {
                    //$type = 'Hard Cover';
                    //} elseif ($document->type == '2') {
                    //$type = 'Book';
                    //}
                    //$file_name = $order_id.'-'.($order_item->product_id ?? 1234).'-'.$document->id.'-'.$order_item_id.' '.$type.'.pdf';
                    //$path = 'downloads/'.$file_name;
                    //$paths[] = $path;
                    //Storage::put($path, $stringBody);

                    //return response()->download(Storage::path($path), $file_name, [], 'inline');
                    //return response()->download(Storage::path($path), $file_name, []);
                } else {

                    abort(404);
                }
            }
        }
        $zipName = 'Order-'.Order::findOrFail($order_id)->order_numeric_id;
        if (Order::findOrFail($order_id)->queue_status != 'processing') {
            $response = ($this->GeneratePDFOrderZipAction)($files, $zipName);
        }
        //$paths = glob($dir . '/*');
        return 'Your File is Being Processed Please try after a few minutes';
    }

    /**
     * Downlaod All PDFs by Generating a ZipFile
     *
     * @param  int  $order_id
     * @return Illuminate\Http\Response
     */

    //public function pdf_download_all_document_order($order_id)
    //{
    //$order_items = Order::findOrFail($order_id)->items;
    //foreach($order_items as $order_item){

    //$order_item_id = $order_item->id;
    //foreach($order_item->product->documents as $document){
    ////Checks if cover is the one selected by the user also check if it isn't a book
    //if(!($document->type == ($order_item->cover['type'] == 2 ? 0 : 1) || $document->type  == 2)){
    //continue;
    //}
    //$data = ($this->GetDocumentInformation)($document->id);

    //$document = $data->document;
    //$product = $document->product;
    //$pages = $data->pages;
    //$dedications = $data->dedications;
    //$barcodes = $data->barcodes;

    //$replace_name = $product->replace_name;
    //$inputs = ($this->GetOrderItemInputs)($order_item_id,$replace_name);

    ////Get pages for where the document is the same
    //$found = null;
    //$pages_array = [];
    //$barcode_array = [];

    ////Search for the correct barcode
    //$barcode_found = ($this->SearchBarcodeWithOrderItemId)($order_item_id,$document->id);

    //$replace_name = $inputs['name'];
    //$replace_age = $inputs['age'];
    //$replace_init = $inputs['init'];

    //foreach ($barcodes as &$barcode) {
    //if ($barcode['document'] == $document->pdf_name) {
    //$barcode_array[] = ['barcode_path' => Storage::path('public/'.$barcode_found['barcode_path']), 'page' => $barcode['page'], 'barcode_info' => $barcode['barcodes']['barcodes'][0]];
    //}
    //}
    //foreach ($pages as &$page) {
    //foreach ($page['pages']['predefined_texts'] as &$page_data) {
    //$page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
    //$page_data['text'] = str_replace('{age}', $replace_age, $page_data['text']);
    //$page_data['text'] = str_replace('{init}', $replace_init, $page_data['text']);
    //}
    //if ($page['document'] == $document->pdf_name) {
    //$pages_array[] = ['page' => $page['page'], 'predefined_texts' => $page['pages']['predefined_texts']];
    //}
    //}
    //$dedications_array = [];

    //$dedication_text = OrderItem::findOrFail($order_item_id)->dedication;
    //foreach ($dedications as &$dedication) {
    //foreach ($dedication['dedications']['dedication_texts'] as &$dedication_data) {
    //$dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_text));
    //$dedication_data['text'] = str_replace('{age}', $replace_age, $dedication_text);
    //$dedication_data['text'] = str_replace('{init}', $replace_init, $dedication_text);
    //}
    //if ($dedication['document'] == $document->pdf_name) {
    //$dedications_array[] = ['page' => $dedication['page'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
    //}
    //}

    //$file = Storage::disk('local')->path('public/'.$document->attatchment);
    //$fonts = Fonts::all();
    //$array = [];
    //if ($fonts) {
    //foreach ($fonts as $font) {
    //$array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment), 'subsetting' => $font->subsetting];
    //}
    //}
    //$array[] = ['font_name' => 'GE-Dinar-Medium', 'attatchment' => public_path('fonts/GE-Dinar-One-Medium.ttf'), 'subsetting' => 0];

    //if (file_exists($file)) {
    //$response = ($this->GeneratePDFOrderAction)($file, $pages_array, $dedications_array, $barcode_array, array_values($array));
    //$body = $response->getBody();
    //// Explicitly cast the body to a string
    //$stringBody = (string) $body;

    //$type = 'Soft Cover';
    //if ($document->type == '1') {
    //$type = 'Hard Cover';
    //} elseif ($document->type == '2') {
    //$type = 'Book';
    //}
    //$file_name = $order_id.'-'.($order_item->product_id ?? 1234).'-'.$document->id.'-'.$order_item_id.' '.$type.'.pdf';
    //$path = 'downloads/'.$file_name;
    //$paths[] = $path;
    //Storage::put($path, $stringBody);

    ////return response()->download(Storage::path($path), $file_name, [], 'inline');
    ////return response()->download(Storage::path($path), $file_name, []);
    //}else{
    //abort(404);
    //}
    //}
    //}

    //return ($this->CreateZipFileFromList)($paths,'Order-'.Order::findOrFail($order_id)->order_numeric_id);

    //}

    /**
     * Preview PDF by sending the request to the PDFGenerator
     *
     * @param  int  $id,$order_item_id
     * @return Illuminate\Http\Response
     */
    public function pdf_preview_document_order($id, $order_item_id)
    {

        $data = ($this->GetDocumentInformation)($id);

        $document = $data->document;
        $product = $document->product;
        $pages = $data->pages;
        $dedications = $data->dedications;
        $barcodes = $data->barcodes;

        $replace_name = $product->replace_name;

        $inputs = ($this->GetOrderItemInputs)($order_item_id, $replace_name);

        $pages_array = [];
        $barcode_array = [];
        $order_item = OrderItem::find($order_item_id);
        $order = Order::findOrFail($order_item->order_id);

        //Search for the correct barcode
        $barcode_found = ($this->SearchBarcodeWithOrderItemId)($order_item_id, $id);

        if ($barcode_found == null) {
            abort(404);
        }
        $replace_name = $inputs['name'];
        $replace_age = $inputs['age'];
        $replace_init = $inputs['init'];

        foreach ($barcodes as &$barcode) {
            if ($barcode['document'] == $document->pdf_name) {
                $barcode_array[] = ['barcode_path' => Storage::path('public/'.$barcode_found['barcode_path']), 'page' => $barcode['page'], 'barcode_info' => $barcode['barcodes']['barcodes'][0]];
            }
        }
        foreach ($pages as &$page) {
            foreach ($page['pages']['predefined_texts'] as &$page_data) {
                $page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
                $page_data['text'] = str_replace('{age}', $replace_age, $page_data['text']);
                $page_data['text'] = str_replace('{init}', $replace_init, $page_data['text']);
            }
            if ($page['document'] == $document->pdf_name) {
                $pages_array[] = ['page' => $page['page'], 'predefined_texts' => $page['pages']['predefined_texts']];
            }
        }
        $dedications_array = [];
        $dedication_text = OrderItem::findOrFail($order_item_id)->dedication;

        foreach ($dedications as &$dedication) {
            foreach ($dedication['dedications']['dedication_texts'] as &$dedication_data) {
                $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_text));
                $dedication_data['text'] = str_replace('{age}', $replace_age, $dedication_text);
                $dedication_data['text'] = str_replace('{init}', $replace_init, $dedication_text);
            }
            if ($dedication['document'] == $document->pdf_name) {
                $dedications_array[] = ['page' => $dedication['page'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
            }
        }

        $file = Storage::disk('local')->path('public/'.$document->attatchment);
        $fonts = Fonts::all();
        $array = [];
        if ($fonts) {
            foreach ($fonts as $font) {
                $array[] = ['font_name' => $font->font_name, 'attatchment' => Storage::path('public/'.$font->attatchment), 'subsetting' => $font->subsetting];
            }
        }
        $array[] = ['font_name' => 'GE-Dinar-Medium', 'attatchment' => public_path('fonts/GE-Dinar-One-Medium.ttf'), 'subsetting' => 0];

        if (file_exists($file)) {
            $response = ($this->GeneratePDFOrderAction)($file, $pages_array, $dedications_array, $barcode_array, array_values($array));
            $body = $response->getBody();
            // Explicitly cast the body to a string
            $stringBody = (string) $body;

            $type = 'Soft Cover';
            if ($document->type == '1') {
                $type = 'Hard Cover';
            } elseif ($document->type == '2') {
                $type = 'Book';
            }
            $file_name = $id.'-'.($order_item->product_id ?? 1234).'-'.$document->id.'-'.$order_item_id.' '.$type.'.pdf';
            $path = 'downloads/'.$file_name;
            Storage::put($path, $stringBody);

            //return response()->download(Storage::path($path), $file_name, [], 'inline');
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

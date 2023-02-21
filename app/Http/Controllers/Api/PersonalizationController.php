<?php

namespace App\Http\Controllers\Api;

use App\Actions\Personalization\GetFontsStylesheetAction;
use App\Http\Controllers\Controller;
use App\Models\Dedication;
use App\Models\Document;
use App\Models\Product;

class PersonalizationController extends Controller
{
    public function __construct(
     GetFontsStylesheetAction $getFontsStylesheetAction
    ) {
        $this->getFontsStylesheetAction = $getFontsStylesheetAction;
    }

    public function get_document_product_slug($slug)
    {
        //Get Product
        $product = Product::where('slug', $slug)->get()->first();
        if (! $product) {
            abort(404);
        }

        $documents = $product->documents;

        return $documents;
    }

    public function get_document_info($slug)
    {
        $product = Product::where('slug', $slug)->get()->first();

        if (! $product) {
            abort(404);
        }
        //Get Product Documents
        $gender = request()->gender;
        $type = request()->type;
        $name = request()->name == '' ? $product->replace_name : request()->name;
        $document = null;

        if ($gender == '' || $type == '' || $name == '') {
            abort(404);
        }

        if ($gender === 'Male') {
            $documents = $product->getMaleDocument();
        }

        if ($gender === 'Female') {
            $documents = $product->getFemaleDocument();
        }

        $documents = $product->getMaleDocument();
        $found_document = null;
        //Find Type of the document
        foreach ($documents as $document) {
            if ($document->type == (int) $type) {
                $found_document = $document;
            }
        }
        //Get Pages from Product
        $document = $found_document;
        $product = $found_document->product;

        if (! $product) {
            abort(404);
        }

        $pages = $product->pagesParsed;
        $dedications = $product->dedicationsParsed;

        $replace_name = $name;
        //Filtered Pages
        $filtered_pages = [];
        $filtered_dedications = [];
        foreach ($pages as $page) {
            if ($page['document'] == $document->name) {
                $filtered_pages[] = ['page' => $page['page'], 'image' => $page['image']['page'], 'dimensions' => $page['image']['dimensions'], 'predefined_texts' => $page['image']['predefined_texts']];
            }
        }

        foreach ($dedications as $dedication) {
            if ($dedication['document'] == $document->name) {
                $filtered_dedications[] = ['page' => $dedication['page'], 'image' => $dedication['image']['page'], 'dimensions' => $dedication['image']['dimensions'], 'dedication_texts' => $dedication['image']['dedication_texts']];
            }
        }

        if ($filtered_pages) {
            foreach ($filtered_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['value']['text'] = trim(preg_replace('/\s\s+/', ' ', str_replace('{basmti}', $replace_name, $page_data['value']['text'])));
                    $page_data = $page_data['value'];
                }
            }
        }

        if ($filtered_dedications) {
            foreach ($filtered_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['value']['text'] = trim(preg_replace('/\s\s+/', ' ', str_replace('{basmti}', $replace_name, $dedication_data['value']['text'])));
                    $dedication_data = $dedication_data['value'];
                }
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['name'] == $document->name) {
                $found_pdf = $pdf;
            }
        }
        //Replace Names
        return ['pages' => $found_pdf, 'pages_predefined_texts' => $filtered_pages];
        //return ['pages' => $filtered_pages, 'dedications' => $filtered_dedications];
    }

    public function get_fonts()
    {
        return ($this->getFontsStylesheetAction)();
    }

    public function get_dedications()
    {
        return Dedication::all();
    }
}

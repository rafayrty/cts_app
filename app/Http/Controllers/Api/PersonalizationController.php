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
        $name = request()->name == '' ? $product->replace_name : request()->name;
        $document = null;

        if ($gender == '' || $name == '') {
            abort(404);
        }

        if ($gender === 'Male') {
            $documents = $product->getMaleDocument();
        }

        if ($gender === 'Female') {
            $documents = $product->getFemaleDocument();
        }

        $found_document = null;
        $found_cover = null;
        //Find Type of the document
        foreach ($documents as $document) {
            if ($document->type == 2) {
                $found_document = $document;
            }
            if ($document->type == 1) {
                $found_cover = $document;
            }
        }
        //Get Pages from Product
        $document = $found_document;
        $cover = $found_cover;
        $product = $found_document->product;

        if (! $product) {
            abort(404);
        }

        $pages = $product->pagesParsed;
        $cover_pages = $product->pagesParsed;
        $dedications = $product->dedicationsParsed;
        $cover_dedications = $product->dedicationsParsed;

        $replace_name = $name;
        //Filtered Pages
        $filtered_pages = [];
        $filtered_dedications = [];
        $filtered_cover_pages = [];
        $filtered_cover_dedications = [];

        foreach ($pages as $page) {
            if ($page['document'] == $document->name) {
                $filtered_pages[] = [
                    'page' => $page['page'],
                    'image' => $page['pages']['page'],
                    'dimensions' => $page['pages']['dimensions'],
                    'predefined_texts' => $page['pages']['predefined_texts'],
                ];
            }
        }

        //Filtering Cover Pages
        foreach ($cover_pages as $cover_page) {
            if ($cover_page['document'] == $cover->name) {
                $filtered_cover_pages[] = [
                    'page' => $cover_page['page'],
                    'image' => $cover_page['pages']['page'],
                    'dimensions' => $cover_page['pages']['dimensions'],
                    'predefined_texts' => $cover_page['pages']['predefined_texts'],
                ];
            }
        }
        //Filtering Dedications
        foreach ($dedications as $dedication) {
            if ($dedication['document'] == $document->name) {
                $filtered_dedications[] = ['page' => $dedication['page'], 'image' => $dedication['dedications']['page'], 'dimensions' => $dedication['dedications']['dimensions'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
            }
        }
        //Filtering Cover Dedications
        foreach ($cover_dedications as $cover_dedication) {
            if ($cover_dedication['document'] == $document->name) {
                $filtered_cover_dedications[] = ['page' => $cover_dedication['page'], 'image' => $cover_dedication['dedications']['page'], 'dimensions' => $cover_dedication['dedications']['dimensions'], 'dedication_texts' => $cover_dedication['dedications']['dedication_texts']];
            }
        }

        //Replacing {basmti}
        if ($filtered_pages) {
            foreach ($filtered_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_dedications) {
            foreach ($filtered_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_cover_pages) {
            foreach ($filtered_cover_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_cover_dedications) {
            foreach ($filtered_cover_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
                }
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['name'] == $found_document->name) {
                $found_pdf = $pdf;
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf_cover = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['name'] == $found_cover->name) {
                $found_pdf_cover = $pdf;
            }
        }
        //Replace Names
        return [
            'pages' => $found_pdf,
            'cover_pages' => $found_pdf_cover,
            'pages_predefined_texts' => $filtered_pages,
            'pages_dedication_texts' => $filtered_dedications,
            'cover_pages_predefined_texts' => $filtered_cover_pages,
            'cover_pages_dedication_texts' => $filtered_cover_dedications,
        ];
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

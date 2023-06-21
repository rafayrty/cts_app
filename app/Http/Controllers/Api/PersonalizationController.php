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

        if (!$product) {
            abort(404);
        }
        //Get Product Documents
        $gender = request()->gender;
        //$name = request()->name == '' ? $product->replace_name : request()->name;

        $inputs = request()->inputs;
        if (! $inputs || $inputs == '{}') {
            $inputs = ['name' => $product->replace_name, 'first_name' => split_name($product->replace_name)[0], 'age' => '٨', 'init' => $product->replace_name[0]];
        } else {
            $inputs = json_decode($inputs, true);
        }

        //Cleaning the json output
        //$newArray = [];
        //foreach ($inputs as $nestedArray) {
        //foreach ($nestedArray as $key => $value) {
        //$newArray[$key] = $value;
        //}
        //}
        //$inputs = $newArray;
        if (array_key_exists('name', $inputs)) {
            $inputs['name'] = $inputs['name'] != '' ? $inputs['name'] : $product->replace_name;
        } else {
            $inputs['name'] = $product->replace_name;
        }
        if (array_key_exists('first_name', $inputs)) {
            $inputs['first_name'] = $inputs['first_name'] != '' ? $inputs['first_name'] : split_name($product->replace_name)[0];
        } else {
            $inputs['first_name'] = split_name($product->replace_name)[0];
        }

        if (array_key_exists('age', $inputs)) {
            $inputs['age'] = $inputs['age'] != '' ? $inputs['age'] : '٨';
        } else {
            $inputs['age'] = '٨';
        }

        if (array_key_exists('init', $inputs)) {
            $inputs['init'] = $inputs['init'] != '' ? $inputs['init'] : $inputs['first_name'][0];
        } else {
            $inputs['init'] = $product->replace_name[0];
        }

        $document = null;

        if ($gender == '' || $inputs['name'] == '') {
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
            if ($document->type == 0 && $found_cover == null) {
                $found_cover = $document;
            }
            if ($document->type == 1 && $found_cover == null) {
                $found_cover = $document;
            }
        }
        if (! $found_document) {
            abort(404);
        }
        if (! $found_cover) {
            abort(404);
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

        $replace_name = $inputs['name'];
        $replace_first_name = $inputs['first_name'];
        $replace_age = $inputs['age'];
        $replace_init = $inputs['init'];

        //Filtered Pages
        $filtered_pages = [];
        $filtered_dedications = [];
        $filtered_cover_pages = [];
        $filtered_cover_dedications = [];

        foreach ($pages as $page) {
            if ($page['document'] == $document->pdf_name) {
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
            if ($cover_page['document'] == $cover->pdf_name) {
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
            if ($dedication['document'] == $document->pdf_name) {
                $filtered_dedications[] = ['page' => $dedication['page'], 'image' => $dedication['dedications']['page'], 'dimensions' => $dedication['dedications']['dimensions'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
            }
        }
        //Filtering Cover Dedications
        foreach ($cover_dedications as $cover_dedication) {
            if ($cover_dedication['document'] == $document->pdf_name) {
                $filtered_cover_dedications[] = ['page' => $cover_dedication['page'], 'image' => $cover_dedication['dedications']['page'], 'dimensions' => $cover_dedication['dedications']['dimensions'], 'dedication_texts' => $cover_dedication['dedications']['dedication_texts']];
            }
        }
        //Replacing {basmti}
        if ($filtered_pages) {
            foreach ($filtered_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
                    $page_data['text'] = str_replace('{f_name}', $replace_first_name, $page_data['text']);
                    $page_data['text'] = str_replace('{age}', $replace_age, $page_data['text']);
                    $page_data['text'] = str_replace('{init}', $replace_init, $page_data['text']);
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_dedications) {
            foreach ($filtered_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
                    $dedication_data['text'] = str_replace('{f_name}', $replace_first_name, $dedication_data['text']);
                    $dedication_data['text'] = str_replace('{age}', $replace_age, $dedication_data['text']);
                    $dedication_data['text'] = str_replace('{init}', $replace_init, $dedication_data['text']);
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_cover_pages) {
            foreach ($filtered_cover_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['text'] = trim(str_replace('{basmti}', $replace_name, $page_data['text']));
                    $page_data['text'] = str_replace('{f_name}', $replace_first_name, $page_data['text']);
                    $page_data['text'] = str_replace('{age}', $replace_age, $page_data['text']);
                    $page_data['text'] = str_replace('{init}', $replace_init, $page_data['text']);
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_cover_dedications) {
            foreach ($filtered_cover_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['text'] = trim(str_replace('{basmti}', $replace_name, $dedication_data['text']));
                    $dedication_data['text'] = str_replace('{f_name}', $replace_first_name, $dedication_data['text']);
                    $dedication_data['text'] = str_replace('{age}', $replace_age, $dedication_data['text']);
                    $dedication_data['text'] = str_replace('{init}', $replace_init, $dedication_data['text']);
                }
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['filename'] == $found_document->pdf_name) {
                $found_pdf = $pdf;
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf_cover = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['filename'] == $found_cover->pdf_name) {
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

    public function get_document_inputs_slug($slug)
    {
        $product = Product::where('slug', $slug)->get()->first();

        if (! $product) {
            abort(404);
        }
        //Get Product Documents
        $gender = request()->gender;
        $name = request()->name == '' ? $product->replace_name : request()->name;
        $document = null;

        $documents = $product->documents;

        $found_document = null;
        $found_cover = null;
        //Find Type of the document
        foreach ($documents as $document) {
            if ($document->type == 2) {
                $found_document = $document;
            }
            if ($document->type == 0) {
                $found_cover = $document;
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
            if ($page['document'] == $document->pdf_name) {
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
            if ($cover_page['document'] == $cover->pdf_name) {
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
            if ($dedication['document'] == $document->pdf_name) {
                $filtered_dedications[] = ['page' => $dedication['page'], 'image' => $dedication['dedications']['page'], 'dimensions' => $dedication['dedications']['dimensions'], 'dedication_texts' => $dedication['dedications']['dedication_texts']];
            }
        }
        //Filtering Cover Dedications
        foreach ($cover_dedications as $cover_dedication) {
            if ($cover_dedication['document'] == $document->pdf_name) {
                $filtered_cover_dedications[] = ['page' => $cover_dedication['page'], 'image' => $cover_dedication['dedications']['page'], 'dimensions' => $cover_dedication['dedications']['dimensions'], 'dedication_texts' => $cover_dedication['dedications']['dedication_texts']];
            }
        }

        //Replacing {basmti}
        if ($filtered_pages) {
            foreach ($filtered_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['text'] = trim($page_data['text']);
                }
            }
        }
        //Replacing {basmti}
        if ($filtered_dedications) {
            foreach ($filtered_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['text'] = trim($dedication_data['text']);
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_cover_pages) {
            foreach ($filtered_cover_pages as &$pages) {
                foreach ($pages['predefined_texts'] as &$page_data) {
                    $page_data['text'] = trim($page_data['text']);
                }
            }
        }

        //Replacing {basmti}
        if ($filtered_cover_dedications) {
            foreach ($filtered_cover_dedications as &$dedications) {
                foreach ($dedications['dedication_texts'] as &$dedication_data) {
                    $dedication_data['text'] = trim($dedication_data['text']);
                }
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['filename'] == $found_document->pdf_name) {
                $found_pdf = $pdf;
            }
        }

        //Filter pdfinfo
        $pdf_info = json_decode($product->pdf_info, true);
        $found_pdf_cover = null;
        foreach ($pdf_info as $pdf) {
            if ($pdf['filename'] == $found_cover->pdf_name) {
                $found_pdf_cover = $pdf;
            }
        }

        $inputs = [];
        ////Get all possible Inputs
        foreach ($filtered_pages as $page) {
            foreach ($page['predefined_texts'] as $texts) {
                if ($this->contains('{f_name}', $texts['text'])) {
                    $inputs[] = 'first_name';
                }
                if ($this->contains('{basmti}', $texts['text'])) {
                    $inputs[] = 'name';
                }
                if ($this->contains('{age}', $texts['text'])) {
                    $inputs[] = 'age';
                }
                if ($this->contains('{init}', $texts['text'])) {
                    $inputs[] = 'init';
                }
            }
        }

        foreach ($filtered_cover_pages as $page) {
            foreach ($page['predefined_texts'] as $texts) {
                if ($this->contains('{f_name}', $texts['text'])) {
                    $inputs[] = 'first_name';
                }
                if ($this->contains('{basmti}', $texts['text'])) {
                    $inputs[] = 'name';
                }
                if ($this->contains('{age}', $texts['text'])) {
                    $inputs[] = 'age';
                }
                if ($this->contains('{init}', $texts['text'])) {
                    $inputs[] = 'init';
                }
            }
        }
        //Get all possible Inputs from dedications
        foreach ($filtered_dedications as $page) {
            foreach ($page['dedication_texts'] as $texts) {
                if ($this->contains('{f_name}', $texts['text'])) {
                    $inputs[] = 'first_name';
                }
                if ($this->contains('{basmti}', $texts['text'])) {
                    $inputs[] = 'name';
                }
                if ($this->contains('{age}', $texts['text'])) {
                    $inputs[] = 'age';
                }
                if ($this->contains('{init}', $texts['text'])) {
                    $inputs[] = 'init';
                }
            }
        }

        foreach ($filtered_cover_dedications as $page) {
            foreach ($page['dedication_texts'] as $texts) {
                if ($this->contains('{f_name}', $texts['text'])) {
                    $inputs[] = 'first_name';
                }
                if ($this->contains('{basmti}', $texts['text'])) {
                    $inputs[] = 'name';
                }
                if ($this->contains('{age}', $texts['text'])) {
                    $inputs[] = 'age';
                }
                if ($this->contains('{init}', $texts['text'])) {
                    $inputs[] = 'init';
                }
            }
        }
        $inputs = array_unique($inputs);

        return array_values($inputs);
    }

    public function get_fonts()
    {
        return ($this->getFontsStylesheetAction)();
    }

    public function get_dedications($gender)
    {
        return Dedication::where('gender', $gender)->get();
    }

    public function contains($needle, $haystack)
    {
        return strpos($haystack, $needle) !== false;
    }
}

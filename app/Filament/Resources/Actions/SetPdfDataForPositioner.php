<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForPositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            if ($search_key != '') {
                $img_page = (int) $get('page');

                $predefined_texts = [];

                if ($get('pages')) {
                    if (array_key_exists('predefined_texts', $get('pages'))) {
                        $predefined_texts = $get('pages')['predefined_texts'];
                    }
                }

                if (array_key_exists($img_page, $json_pdfs[$search_key]['pdf'])) {
                    return [
                        'dimensions' => $json_pdfs[$search_key]['dimensions'],
                        'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                        'type' => $json_pdfs[$search_key]['type'],
                        'page_number' => $get('page'),
                        'predefined_texts' => $predefined_texts,
                    ];
                }
            }
        }

        return [];
    }

    public function searchkey($array, $search)
    {
        $key = null;
        if($array){
            foreach ($array as $key => $value) {
                if ($value['filename'] == $search) {
                    return $key;
                }
            }
        }
    }
}

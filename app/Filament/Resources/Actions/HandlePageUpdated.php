<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePageUpdated
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('document') != '' && $get('../../pdf_info') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            $img_page = (int) $get('page');

            $predefined_texts = [];
            if ($get('pages')) {
                if (array_key_exists('predefined_texts', $get('pages'))) {
                    $predefined_texts = $get('pages')['predefined_texts'];
                }
            }
            $set('pages',
                [
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                    'type' => $json_pdfs[$search_key]['type'],
                    'page_number' => $get('page'),
                    'predefined_texts' => $predefined_texts,
                ]);
        }
    }

    public function searchkey($array, $search)
    {
        $key = null;
        foreach ($array as $key => $value) {
            if ($value['name'] == $search) {
                return $key;
            }
        }
    }
}

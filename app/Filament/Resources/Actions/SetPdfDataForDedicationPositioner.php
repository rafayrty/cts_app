<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForDedicationPositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            if ($search_key != '') {
                $img_page = (int) $get('page');

                $dedication_texts = [];

                if ($get('dedications')) {
                    if (array_key_exists('dedication_texts', $get('dedications'))) {
                        $dedication_texts = $get('dedications')['dedication_texts'];
                    }
                }

                return [
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                    'page_number' => $get('page'),
                    'dedication_texts' => $dedication_texts,
                ];
            }
        }

        return [];
    }

    public function searchkey($array, $search)
    {
        $key = null;
        foreach ($array as $key => $value) {
            if ($value['filename'] == $search) {
                return $key;
            }
        }
    }
}

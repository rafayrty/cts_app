<?php

namespace App\Filament\Resources\Actions;

use Closure;

class SetPdfDataForBarcodePositioner
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        if ($get('page') != '') {
            $json_pdfs = json_decode($get('../../pdf_info'), true);
            $search_key = $this->searchkey($json_pdfs, $get('document'));
            //$json_pdfs[$search_key]['type'] = $get('page');
            if ($search_key) {
                $img_page = (int) $get('page');

                $barcodes = [];

                if ($get('barcodes')) {
                    if (array_key_exists('barcodes', $get('barcodes'))) {
                        $barcodes = $get('barcodes')['barcodes'];
                    }
                }

                return [
                    'dimensions' => $json_pdfs[$search_key]['dimensions'],
                    'page' => asset($json_pdfs[$search_key]['pdf'][$img_page]),
                    'page_number' => $get('page'),
                    'barcodes' => $barcodes,
                ];
            }
        }

        return [];
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

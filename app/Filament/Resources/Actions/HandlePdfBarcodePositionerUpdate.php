<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePdfBarcodePositionerUpdate
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        $barcode_info = $state['barcode_info'];
        foreach ($barcode_info as $text) {
            $set('barcode_info.'.$text['field_key'].'.max_width', $text['value']['max_width']);
            $set('barcode_info.'.$text['field_key'].'.width_percent', $text['value']['width_percent']);
            $set('barcode_info.'.$text['field_key'].'.X_coord', $text['value']['X_coord']);
            $set('barcode_info.'.$text['field_key'].'.Y_coord', $text['value']['Y_coord']);
            $set('barcode_info.'.$text['field_key'].'.X_coord_percent', $text['value']['X_coord_percent']);
            $set('barcode_info.'.$text['field_key'].'.Y_coord_percent', $text['value']['Y_coord_percent']);
        }
    }
}

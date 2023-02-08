<?php

namespace App\Filament\Resources\Actions;

use Closure;

class HandlePdfDedicationPositionerUpdate
{
    public function __invoke(Closure $set, Closure $get, $state)
    {
        $dedication_texts = $state['dedication_texts'];
        foreach ($dedication_texts as $text) {
            $set('dedication_texts.'.$text['field_key'].'.max_width', $text['value']['max_width']);
            $set('dedication_texts.'.$text['field_key'].'.width_percent', $text['value']['width_percent']);
            $set('dedication_texts.'.$text['field_key'].'.X_coord', $text['value']['X_coord']);
            $set('dedication_texts.'.$text['field_key'].'.Y_coord', $text['value']['Y_coord']);
            $set('dedication_texts.'.$text['field_key'].'.X_coord_percent', $text['value']['X_coord_percent']);
            $set('dedication_texts.'.$text['field_key'].'.Y_coord_percent', $text['value']['Y_coord_percent']);
            $set('dedication_texts.'.$text['field_key'].'.text', $text['value']['text']);
        }
    }
}

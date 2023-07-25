<?php

namespace App\Actions\Personalization;

use App\Models\OrderItem;

class GetOrderItemInputs
{
    public function __invoke($id, $replace_name)
    {
        $inputs = OrderItem::find($id)->inputs;

        if (array_key_exists('name', $inputs)) {
            $inputs['name'] = $inputs['name'] != '' ? $inputs['name'] : $replace_name;
        } else {
            $inputs['name'] = $replace_name;
        }

        if (array_key_exists('age', $inputs)) {
            $inputs['age'] = $inputs['age'] != '' ? $inputs['age'] : '٨';
        } else {
            $inputs['age'] = '٨';
        }

        if (array_key_exists('init', $inputs)) {
            $inputs['init'] = $inputs['init'] != '' ? $inputs['init'] : mb_substr($inputs['name'], 0, 1, 'UTF-8');
        } else {
            $inputs['init'] = mb_substr($inputs['name'], 0, 1, 'UTF-8');
        }

        return $inputs;
    }
}

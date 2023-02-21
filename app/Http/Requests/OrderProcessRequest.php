<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'creditCard' => 'required',
            'cvv' => 'required',
            'cardHolderName' => 'required',
            'expiry' => 'required',
            'id_number' => 'required',
            'postcode' => 'required',

            //Cart Validation
            'discount_total' => 'required',
            'subtotal' => 'required',
            'total' => 'required',
            'address' => 'required',
            'items.*.product_name' => 'required',
            'items.*.price' => 'required',
            'items.*.total' => 'required',
            'items.*.id' => 'required',
            'items.*.addons_price' => 'required',

        ];
    }
}

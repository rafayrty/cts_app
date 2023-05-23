<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone' => 'required',
            'country_code' => 'required',
            'city' => 'required|string|max:50',
            'street_name' => 'required|string|max:255',
            'street_number' => 'required|string|max:255',
            'home_no' => 'required|string|max:50',
        ];
    }
}

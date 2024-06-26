<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginPhoneRequest extends FormRequest
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
            'phone' => 'required|max:255',
            'country_code' => 'required|max:255',
            'password' => 'required|min:6|max:32',
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email,'.$this->user()->id,
            'phone' => 'required',
            'country_code' => 'required',
            'new_password' => 'required_with_all:new_password_confirmation,current_passsword|same:new_password_confirmation',
            'new_password_confirmation' => 'min:6',
        ];
    }
}

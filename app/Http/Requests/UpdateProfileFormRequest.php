<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  =>  ['nullable', 'string', 'min:3', 'max:25'],
            'email'  =>  ['nullable', 'email'],
            'password'  =>  ['nullable', 'min:8', 'max:32', 'confirmed'],
            'phone_number' => ['nullable', 'numeric'],
            'avatar' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png'], // max size is 2048 KB
            'official_id' => ['nullable', 'file', 'max:2048', 'mimes:jpg,jpeg,png'], // max size is 2048 KB
            'country_id' => ['nullable', 'exists:countries,id'],
            'state_id' => ['nullable', 'exists:states,id'],
            'address_line_1' => ['nullable', 'max:32', 'string'],
            'zip_code' => ['nullable', 'digits_between:0,5', 'numeric'],
        ];
    }
}

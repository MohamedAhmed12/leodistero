<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserFormRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'  =>  ['required', 'string', 'min:3', 'max:25'],
            'email'  =>  ['required', 'email'],
            'password'  =>  ['required', 'min:8', 'max:32', 'confirmed'],
            'phone_number' => ['required', 'numeric'],
        ];
    }
}
